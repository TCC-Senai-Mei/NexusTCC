<?php

namespace App\Controllers;

use App\Models\ConversationModel;
use App\Models\ChatMessageModel;
use App\Models\NotificationModel;
use App\Models\ProtocolModel;
use CodeIgniter\Controller;

class Chat extends Controller
{
    // ─── FAQ Tree ─────────────────────────────────────────────────────────────
    private static array $faq = [
        '__menu__' => [
            'answer'  => "Olá! Sou o **Assistente Nexus** da Sala do Empreendedor de Nova Lima. 👋\n\nComo posso te ajudar hoje?",
            'options' => ['Declaração Anual', 'DAS (Boleto Mensal)', 'Alvará de Funcionamento', 'Quero falar com um atendente'],
        ],
        'Declaração Anual' => [
            'answer'  => "A **Declaração Anual do MEI (DASN-SIMEI)** deve ser entregue até 31 de maio de cada ano.\n\n**Passo a passo:**\n1. Acesse gov.br/receitafederal\n2. Clique em \"Declaração Anual do MEI\"\n3. Informe o faturamento do ano anterior\n4. Envie e guarde o comprovante",
            'options' => ['Já tentei e não consigo acessar', 'Qual é a multa por atraso?', 'Voltar ao menu principal'],
        ],
        'DAS (Boleto Mensal)' => [
            'answer'  => "O **DAS** é o boleto mensal do MEI, com vencimento todo dia 20.\n\n**Para emitir o DAS:**\n1. Acesse o Portal do Empreendedor (gov.br/mei)\n2. Clique em \"Pagar DAS\"\n3. Informe seu CNPJ e baixe o boleto\n\nBoletos em atraso geram juros de 1% ao mês + correção SELIC.",
            'options' => ['Tenho DAS atrasado, e agora?', 'Meu banco não aceita o boleto', 'Voltar ao menu principal'],
        ],
        'Alvará de Funcionamento' => [
            'answer'  => "O **Alvará de Funcionamento** é emitido pela Prefeitura de Nova Lima.\n\n1. Clique em \"Meu Portal MEI\" no menu lateral\n2. Abra um novo protocolo com a categoria \"Alvará\"\n3. Anexe: CNPJ, comprovante de endereço e contrato de aluguel\n4. Acompanhe o status no seu painel\n\nPrazo médio de análise: **5 dias úteis**.",
            'options' => ['Meu alvará venceu', 'Mudei de endereço', 'Voltar ao menu principal'],
        ],
        'Tenho DAS atrasado, e agora?' => [
            'answer'  => "Você pode regularizar os DAS atrasados pelo **PGMEI** no Portal do Empreendedor — os boletos são gerados com encargos calculados automaticamente.\n\nSe precisar de orientação presencial, posso abrir um atendimento com um servidor.",
            'options' => ['Quero falar com um atendente', 'Voltar ao menu principal'],
        ],
        'Meu banco não aceita o boleto' => [
            'answer'  => "Boletos do DAS podem ser pagos em qualquer banco, lotérica ou pelo aplicativo do seu banco via código de barras.\n\nSe o problema persistir, acesse o site do PGMEI e gere um novo boleto — o código pode ter expirado.",
            'options' => ['Voltar ao menu principal'],
        ],
        'Meu alvará venceu' => [
            'answer'  => "Para renovar o alvará vencido, o processo é o mesmo da emissão inicial. Vou encaminhar seu atendimento para um servidor da Sala do Empreendedor.",
            'human'   => true,
            'options' => ['Voltar ao menu principal'],
        ],
        'Mudei de endereço' => [
            'answer'  => "Para atualizar o endereço comercial:\n1. Acesse o Portal do Empreendedor (gov.br/mei)\n2. Vá em \"Alterar dados do MEI\"\n3. Após a atualização, solicite um novo alvará aqui no portal\n\nPosso abrir um atendimento se precisar de ajuda.",
            'options' => ['Quero falar com um atendente', 'Voltar ao menu principal'],
        ],
        'Já tentei e não consigo acessar' => [
            'answer'  => "Dificuldades para acessar o gov.br são comuns. Tente:\n\n1. Verificar se seu CPF está ativo no gov.br\n2. Recuperar o acesso em **gov.br/recuperar-acesso**\n3. Entrar em contato com a Sala do Empreendedor\n\nPosso te conectar a um atendente agora.",
            'options' => ['Quero falar com um atendente', 'Voltar ao menu principal'],
        ],
        'Qual é a multa por atraso?' => [
            'answer'  => "A multa por atraso na Declaração Anual é de **R$ 50,00** mínimo, acrescida de 0,33% ao dia — limitada a 20% do imposto. Quanto antes regularizar, menor a multa!",
            'options' => ['Como regularizar?', 'Voltar ao menu principal'],
        ],
        'Como regularizar?' => [
            'answer'  => "Para regularizar a Declaração Anual atrasada:\n1. Acesse gov.br/receitafederal\n2. Faça a declaração normalmente\n3. O sistema calculará a multa automaticamente\n4. Pague o DAS com a multa incluída",
            'options' => ['Quero falar com um atendente', 'Voltar ao menu principal'],
        ],
        'Quero falar com um atendente' => [
            'answer'  => "Entendido! Estou gerando um **protocolo de atendimento** e conectando você a um servidor da Sala do Empreendedor. Você receberá uma resposta em até 1 dia útil.",
            'human'   => true,
            'options' => ['Voltar ao menu principal'],
        ],
        'Voltar ao menu principal' => [
            'answer'  => "Claro! Aqui estão os principais tópicos. Como posso te ajudar?",
            'options' => ['Declaração Anual', 'DAS (Boleto Mensal)', 'Alvará de Funcionamento', 'Quero falar com um atendente'],
        ],
    ];

    // ─── Página principal do chat ─────────────────────────────────────────────
    public function index()
    {
        $userId = session()->get('user_id');
        $role   = session()->get('user_role');

        $convModel  = new ConversationModel();
        $notifModel = new NotificationModel();

        // MEI: suas conversas / Servidor: conversas humanas pendentes
        $conversations = ($role === 'servidor')
            ? $convModel->getHumanConversations()
            : $convModel->getByUser($userId);

        $user = [
            'id'   => $userId,
            'name' => session()->get('user_name'),
            'role' => $role,
            'cnpj' => session()->get('user_cnpj'),
        ];

        $data = [
            'user'          => $user,
            'notifications' => $notifModel->getByUser($userId),
            'conversations' => $conversations,
            'active_page'   => 'chat',
        ];

        $innerView = view('chat/index', $data);
        return view('layouts/main', array_merge($data, ['page_content' => $innerView]));
    }

    // ─── AJAX: criar nova conversa ────────────────────────────────────────────
    public function novaConversa()
    {
        $userId    = session()->get('user_id');
        $convModel = new ConversationModel();
        $msgModel  = new ChatMessageModel();

        $input = $this->request->getJSON(true);
        $titulo = $input['titulo'] ?? 'Nova conversa';

        // Cria conversa
        $convModel->insert([
            'user_id' => $userId,
            'titulo'  => $titulo,
            'tipo'    => 'bot',
            'status'  => 'ativo',
        ]);
        $convId = $this->db->insertID();

        // Salva mensagem de boas-vindas do bot
        $menu = self::$faq['__menu__'];
        $msgModel->saveBot($convId, $menu['answer'], $menu['options']);

        return $this->response->setJSON([
            'ok'              => true,
            'conversation_id' => $convId,
        ]);
    }

    // ─── AJAX: carregar mensagens de uma conversa ─────────────────────────────
    public function mensagens(int $convId)
    {
        $userId    = session()->get('user_id');
        $role      = session()->get('user_role');
        $convModel = new ConversationModel();
        $msgModel  = new ChatMessageModel();

        $conv = $convModel->find($convId);
        if (!$conv) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Conversa não encontrada']);
        }

        // Verifica permissão
        if ($role === 'mei' && $conv['user_id'] != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Sem permissão']);
        }

        // Marca como lidas
        if ($role === 'servidor') {
            $msgModel->markReadByServer($convId);
            // Muda status para em_atendimento
            if ($conv['status'] === 'aguardando_servidor') {
                $convModel->update($convId, [
                    'status'      => 'em_atendimento',
                    'servidor_id' => $userId,
                ]);
            }
        } else {
            $msgModel->markReadByMei($convId);
        }

        $msgs = $msgModel->getByConversation($convId);

        // Deserializa opções para array
        foreach ($msgs as &$m) {
            $m['opcoes'] = $m['opcoes'] ? json_decode($m['opcoes'], true) : [];
        }

        return $this->response->setJSON([
            'ok'           => true,
            'conversation' => $conv,
            'messages'     => $msgs,
        ]);
    }

    // ─── AJAX: polling — novas mensagens após um ID ───────────────────────────
    public function poll(int $convId)
    {
        $input   = $this->request->getJSON(true) ?? [];
        $afterId = (int)($input['after_id'] ?? 0);

        $msgModel = new ChatMessageModel();
        $msgs = $msgModel->getAfter($convId, $afterId);

        foreach ($msgs as &$m) {
            $m['opcoes'] = $m['opcoes'] ? json_decode($m['opcoes'], true) : [];
        }

        return $this->response->setJSON(['messages' => $msgs]);
    }

    // ─── AJAX: resposta do bot (MEI envia opção/texto) ────────────────────────
    public function resposta()
    {
        $input  = $this->request->getJSON(true);
        $convId = (int)($input['conversation_id'] ?? 0);
        $texto  = trim($input['mensagem'] ?? '');
        $userId = session()->get('user_id');

        if (!$convId || !$texto) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Dados inválidos']);
        }

        $convModel = new ConversationModel();
        $msgModel  = new ChatMessageModel();
        $conv = $convModel->find($convId);

        if (!$conv || $conv['user_id'] != $userId) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Sem permissão']);
        }

        // Salva mensagem do MEI
        $msgModel->saveUser($convId, $userId, 'mei', $texto);

        // Se a conversa já é humana, não responde como bot
        if ($conv['tipo'] === 'humano') {
            $convModel->update($convId, ['updated_at' => date('Y-m-d H:i:s')]);
            return $this->response->setJSON(['ok' => true, 'tipo' => 'humano']);
        }

        // Busca resposta no FAQ
        $faqKey = $texto;
        $resp   = self::$faq[$faqKey] ?? null;

        if (!$resp) {
            // Tenta match parcial
            foreach (self::$faq as $k => $v) {
                if (stripos($texto, $k) !== false || stripos($k, $texto) !== false) {
                    $resp   = $v;
                    $faqKey = $k;
                    break;
                }
            }
        }

        if (!$resp) {
            $msgModel->saveBot($convId,
                "Não entendi sua mensagem. Escolha uma das opções abaixo ou escreva uma dúvida mais específica.",
                ['Declaração Anual', 'DAS (Boleto Mensal)', 'Alvará de Funcionamento', 'Quero falar com um atendente']
            );
            return $this->response->setJSON(['ok' => true, 'tipo' => 'bot']);
        }

        $protocol = null;

        // Escalar para humano
        if (!empty($resp['human'])) {
            $protModel = new ProtocolModel();
            $protocol  = $protModel->generateNumber();
            $protModel->insert([
                'protocol_number' => $protocol,
                'user_id'         => $userId,
                'descricao'       => $faqKey,
                'categoria'       => 'Chatbot',
                'status'          => 'Em Análise',
                'canal'           => 'Chatbot',
            ]);
            $convModel->escalarParaHumano($convId, $protocol);
            // Atualiza título da conversa
            $convModel->update($convId, ['titulo' => $faqKey]);
        }

        // Atualiza título da conversa com o primeiro tópico selecionado
        if ($conv['titulo'] === 'Nova conversa' && isset(self::$faq[$faqKey])) {
            $convModel->update($convId, ['titulo' => $faqKey]);
        }

        $msgModel->saveBot($convId, $resp['answer'], $resp['options'] ?? [], $protocol);
        $convModel->update($convId, ['updated_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['ok' => true, 'tipo' => 'bot']);
    }

    // ─── AJAX: servidor envia resposta humana ─────────────────────────────────
    public function responderServidor()
    {
        $input    = $this->request->getJSON(true);
        $convId   = (int)($input['conversation_id'] ?? 0);
        $mensagem = trim($input['mensagem'] ?? '');
        $userId   = session()->get('user_id');
        $role     = session()->get('user_role');

        if ($role !== 'servidor' || !$convId || !$mensagem) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Sem permissão ou dados inválidos']);
        }

        $convModel = new ConversationModel();
        $msgModel  = new ChatMessageModel();

        $msgModel->saveUser($convId, $userId, 'servidor', $mensagem);
        $convModel->update($convId, [
            'status'      => 'em_atendimento',
            'servidor_id' => $userId,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['ok' => true]);
    }

    // ─── Helper: db ───────────────────────────────────────────────────────────
    protected function db()
    {
        return \Config\Database::connect();
    }
}
