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

        // Injeta a view interna do chat e envelopa dentro do layout master (main.php)
        $innerView = view('chat/index', $data);
        return view('layouts/main', array_merge($data, ['page_content' => $innerView]));
    }

    /**
     * API: Lista as sessões do usuário logado
     */
    public function listarSessoes()
    {
        $userId = session()->get('user_id');
        $sessoes = $this->sessionModel->getHistoricoComUltimaMensagem($userId);
        return $this->response->setJSON($sessoes);
    }

    /**
     * API: Cria uma nova sessão com a mensagem de boas-vindas do menu inicial do bot
     */
    public function criarSessao()
    {
        $userId = session()->get('user_id');

        $sessionId = $this->sessionModel->insert([
            'user_id' => $userId,
            'titulo'  => 'Atendimento Assistente',
            'tipo'    => 'bot',
            'status'  => 'ativo'
        ]);

        $menuInicial = $this->botService->obterRespostaPorId('__menu__');

        // Salva a primeira mensagem usando o método herdado e adaptado do seu ChatMessageModel antigo
        $this->messageModel->saveBot($sessionId, $menuInicial['texto'], $menuInicial['opcoes']);

        return $this->response->setJSON(['status' => 'sucesso', 'session_id' => $sessionId]);
    }

    /**
     * API: Retorna o histórico cronológico de mensagens de uma sessão específica
     */
    public function carregarMensagens(int $sessionId)
    {
        $mensagens = $this->messageModel->getMensagensSessao($sessionId);
        
        // Decodifica a string JSON de opções salva no banco para array nativo
        foreach ($mensagens as &$msg) {
            $msg['opcoes'] = $msg['opcoes'] ? json_decode($msg['opcoes'], true) : [];
        }

        return $this->response->setJSON($mensagens);
    }

    /**
     * API: Recebe o input do usuário ou o clique em uma quick reply e calcula o próximo passo do bot
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
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Parâmetros inválidos']);
        }

        // 1. Persiste a mensagem que o usuário digitou ou clicou
        $this->messageModel->saveUser($sessionId, $userId, $userRole, $texto);

        // 2. Se for texto livre, checa se aciona palavra-chave de transição para humano
        if ($texto && !$opcaoId && $this->botService->avaliarFallback($texto)) {
            $opcaoId = 'fluxo_humano';
        }

        // 3. Se caiu no fluxo humano, gera o protocolo e fecha a sessão com o bot
        if ($opcaoId === 'fluxo_humano') {
            $protocolo = $this->protocolService->gerarProtocolo();

            // Insere na tabela global de protocolos do sistema para listagem geral
            $db = \Config\Database::connect();
            $db->table('protocols')->insert([
                'protocol_number' => $protocolo,
                'user_id'         => $userId,
                'descricao'       => 'Atendimento transferido via Chatbot / Assistente Virtual.',
                'categoria'       => 'Suporte Técnico',
                'status'          => 'Pendente',
                'canal'           => 'Chat'
            ]);

            // Mensagem especial de transição
            $textoHumano = "Entendido. Para dar continuidade, transferi sua solicitação para a fila de atendimento dos funcionários da Prefeitura.\n\nSeu protocolo é **{$protocolo}**.\nVocê pode acompanhar o status na aba Protocolos.";
            $this->messageModel->saveBot($sessionId, $textoHumano, [], $protocolo);

            // Atualiza o status da sessão para histórico (encerra o ciclo do robô)
            $this->sessionModel->update($sessionId, [
                'tipo'            => 'humano',
                'status'          => 'aguardando_servidor',
                'protocol_number' => $protocolo
            ]);

            return $this->response->setJSON(['status' => 'sucesso']);
        }

        // 4. Caso contrário, segue a árvore normal de decisão
        $nodoId = $opcaoId ?: '__menu__';
        $respostaBot = $this->botService->obterRespostaPorId($nodoId);

        $this->messageModel->saveBot($sessionId, $respostaBot['texto'], $respostaBot['opcoes']);

        return $this->response->setJSON(['status' => 'sucesso']);
    }
}