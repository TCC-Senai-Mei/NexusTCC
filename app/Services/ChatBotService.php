<?php

namespace App\Services;

class ChatBotService
{
    // Árvore de decisão estruturada com IDs explícitos solicitados
    private array $arvore = [
        '__menu__' => [
            'texto' => "Olá! Bem-vindo ao Nexus, o assistente virtual da Sala do Empreendedor. Como posso te ajudar hoje?",
            'opcoes' => [
                ['label' => '1 → Quero abrir um MEI', 'id' => 'fluxo_abrir_mei'],
                ['label' => '2 → Quero registrar meu CNPJ', 'id' => 'fluxo_registrar_cnpj'],
                ['label' => '3 → Serviços Regulares (DAS/Alvará)', 'id' => 'fluxo_servicos'],
                ['label' => '4 → Falar com Atendente Humano', 'id' => 'fluxo_humano']
            ]
        ],
        'fluxo_abrir_mei' => [
            'texto' => "Para abrir um MEI de forma gratuita, você precisa:\n\n• Ter conta Gov.br (Prata ou Ouro);\n• Escolher atividade permitida;\n• Definir local de atuação.\n\nDeseja verificar as atividades ou prefere agendar um auxílio presencial na prefeitura?",
            'opcoes' => [
                ['label' => 'Ver Atividades Permitidas', 'id' => 'msg_final_mei'],
                ['label' => 'Agendar Atendimento Presencial', 'id' => 'fluxo_humano']
            ]
        ],
        'msg_final_mei' => [
            'texto' => "Você pode consultar a lista completa de atividades permitidas diretamente no Portal do Empreendedor do Governo Federal. Deseja voltar ao início?",
            'opcoes' => [
                ['label' => 'Voltar ao Início', 'id' => 'inicio']
            ]
        ],
        'fluxo_registrar_cnpj' => [
            'texto' => "Se você está abrindo uma Microempresa (ME) ou Empresa de Pequeno Porte (EPP), o registro não é automático como o MEI.\n\nÉ necessário apoio de um contador e viabilidade da prefeitura através da Junta Comercial.",
            'opcoes' => [
                ['label' => 'Voltar ao Início', 'id' => 'inicio'],
                ['label' => 'Falar com Suporte', 'id' => 'fluxo_humano']
            ]
        ],
        'fluxo_servicos' => [
            'texto' => "Para emissão de DAS atrasada, parcelamentos ou consultas de Alvará, acesse diretamente o Portal do MEI autenticado.",
            'opcoes' => [
                ['label' => 'Voltar ao Início', 'id' => 'inicio']
            ]
        ]
    ];

    /**
     * Busca o nó correto da árvore com base no ID da opção clicada.
     */
    public function obterRespostaPorId(string $id): array
    {
        if ($id === 'inicio' || !isset($this->arvore[$id])) {
            return $this->arvore['__menu__'];
        }
        return $this->arvore[$id];
    }

    /**
     * Verifica se o texto digitado livremente ativa a regra de fallback (suporte humano)
     */
    public function avaliarFallback(string $mensagem): bool
    {
        $msg = mb_strtolower(trim($mensagem));
        
        return (
            strpos($msg, 'atendente') !== false || 
            strpos($msg, 'humano')    !== false || 
            strpos($msg, 'ajuda')     !== false
        );
    }
}