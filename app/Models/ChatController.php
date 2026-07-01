<?php

namespace App\Controllers;

use App\Models\ChatSessionModel;
use App\Models\ChatMessageModel;
use App\Services\ChatBotService;
use App\Services\ProtocolGeneratorService;
use CodeIgniter\Controller;

class ChatController extends Controller
{
    protected ChatSessionModel $sessionModel;
    protected ChatMessageModel $messageModel;
    protected ChatBotService $botService;
    protected ProtocolGeneratorService $protocolService;

    public function __construct()
    {
        $this->sessionModel    = new ChatSessionModel();
        $this->messageModel    = new ChatMessageModel();
        $this->botService       = new ChatBotService();
        $this->protocolService  = new ProtocolGeneratorService();
    }

    /**
     * Renderiza a página principal do chat no painel
     */
    public function index()
    {
        $userId   = session()->get('user_id');
        $userName = session()->get('user_name');
        
        $data = [
            'user'        => ['id' => $userId, 'name' => $userName],
            'active_page' => 'chat',
        ];

        $innerView = view('chat/index', $data);
        return view('layouts/main', array_merge($data, ['page_content' => $innerView]));
    }

    /**
     * API REST: Lista todas as sessões pertencentes ao usuário logado
     */
    public function listarSessoes()
    {
        $userId = session()->get('user_id');
        $sessoes = $this->sessionModel->getHistoricoComUltimaMensagem($userId);
        return $this->response->setJSON($sessoes);
    }

    /**
     * API REST: Cria um novo atendimento isolado e injeta o menu de boas-vindas do Bot
     */
    public function criarSessao()
    {
        $userId = session()->get('user_id');

        // Cria a sessão na tabela usando o Model correto
        $sessionId = $this->sessionModel->insert([
            'user_id' => $userId,
            'titulo'  => 'Atendimento Assistente',
            'tipo'    => 'bot',
            'status'  => 'ativo'
        ]);

        if (!$sessionId) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Erro ao criar sessão no banco.']);
        }

        // Obtém o menu de boas-vindas do serviço do robô
        $menuInicial = $this->botService->obterRespostaPorId('__menu__');

        // Salva a primeira mensagem vinculando ao ID retornado
        $this->messageModel->saveBot((int)$sessionId, $menuInicial['texto'], $menuInicial['opcoes']);

        // Retorna exatamente o que o chatbot.js espera ler
        return $this->response->setJSON([
            'status'     => 'sucesso',
            'session_id' => (int)$sessionId
        ]);
    }

    /**
     * API REST: Retorna o histórico de mensagens de uma sessão específica
     */
    public function carregarMensagens(int $sessionId)
    {
        $mensagens = $this->messageModel->getMensagensSessao($sessionId);
        return $this->response->setJSON($mensagens);
    }

    /**
     * API REST: Processa o envio de mensagens do usuário (via texto livre ou clique de botão)
     */
    public function enviarMensagem()
    {
        $req = $this->request->getJSON(true);
        
        $sessionId = (int)($req['session_id'] ?? 0);
        $texto     = trim($req['message'] ?? '');
        $opcaoId   = trim($req['opcao_id'] ?? '');

        $userId   = session()->get('user_id');
        $userRole = session()->get('user_role') ?? 'mei';

        if (!$sessionId || (!$texto && !$opcaoId)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Parâmetros de requisição inválidos.']);
        }

        // 1. Grava a mensagem digitada/clicada pelo usuário no banco
        $this->messageModel->saveUser($sessionId, $userId, $userRole, $texto);

        // 2. Validação de Fallback Textual (Se o usuário digitou palavras como "ajuda", "humano", "atendente")
        if ($texto && !$opcaoId && $this->botService->avaliarFallback($texto)) {
            $opcaoId = 'fluxo_humano';
        }

        // 3. Se cair no fluxo de transição humana, gera o protocolo e altera o status da sessão
        if ($opcaoId === 'fluxo_humano') {
            $protocolo = $this->protocolService->gerarProtocolo();

            $db = \Config\Database::connect();
            $db->table('protocols')->insert([
                'protocol_number' => $protocolo,
                'user_id'         => $userId,
                'descricao'       => 'Atendimento transferido via Chatbot / Assistente Virtual.',
                'categoria'       => 'Suporte Técnico',
                'status'          => 'Pendente',
                'canal'           => 'Chat'
            ]);

            $textoHumano = "Entendido. Para dar continuidade, transferi sua solicitação para a fila de atendimento dos funcionários da Prefeitura.\n\nSeu protocolo é **{$protocolo}**.\nVocê pode acompanhar o status na aba Protocolos.";
            $this->messageModel->saveBot($sessionId, $textoHumano, [], $protocolo);

            $this->sessionModel->update($sessionId, [
                'tipo'            => 'humano',
                'status'          => 'aguardando_servidor',
                'protocol_number' => $protocolo
            ]);

            return $this->response->setJSON(['status' => 'sucesso']);
        }

        // 4. Caso contrário, segue o fluxo normal da árvore mapeada do robô
        $nodoId = $opcaoId ?: '__menu__';
        $respostaBot = $this->botService->obterRespostaPorId($nodoId);

        $this->messageModel->saveBot($sessionId, $respostaBot['texto'], $respostaBot['opcoes']);

        // Atualiza o timestamp da sessão para subir na listagem lateral
        $this->sessionModel->update($sessionId, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'sucesso']);
    }
}