<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table      = 'chat_messages'; // Tabela atualizada na migration
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'session_id', 'sender_id', 'sender_role', 'message', 'opcoes', 'protocol_number', 'is_read'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /**
     * Recupera o histórico completo de mensagens de uma sessão específica 
     * ordenado de forma cronológica crescente.
     */
    public function getMensagensSessao(int $sessionId): array
    {
        return $this->where('session_id', $sessionId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    // Mensagens novas após um ID (polling/atualização reativa no front)
    public function getAfter(int $sessionId, int $afterId): array
    {
        return $this->where('session_id', $sessionId)
                    ->where('id >', $afterId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    // Salvar mensagem do bot com opções
    public function saveBot(int $sessionId, string $message, array $opcoes = [], string $protocol = null): int
    {
        $this->insert([
            'session_id'      => $sessionId,
            'sender_id'       => null,
            'sender_role'     => 'bot',
            'message'         => $message,
            'opcoes'          => $opcoes ? json_encode($opcoes, JSON_UNESCAPED_UNICODE) : null,
            'protocol_number' => $protocol,
        ]);
        return $this->db->insertID();
    }

    // Salvar mensagem do usuário (MEI ou Servidor)
    public function saveUser(int $sessionId, int $senderId, string $role, string $message): int
    {
        $this->insert([
            'session_id'  => $sessionId,
            'sender_id'   => $senderId,
            'sender_role' => $role,
            'message'     => $message,
        ]);
        return $this->db->insertID();
    }

    // Marcar como lidas (pelo servidor)
    public function markReadByServer(int $sessionId): void
    {
        $this->where('session_id', $sessionId)
             ->where('sender_role', 'mei')
             ->set(['is_read' => 1])
             ->update();
    }

    // Marcar como lidas (pelo MEI)
    public function markReadByMei(int $sessionId): void
    {
        $this->where('session_id', $sessionId)
             ->whereIn('sender_role', ['bot', 'servidor'])
             ->set(['is_read' => 1])
             ->update();
    }
}