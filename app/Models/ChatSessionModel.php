<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatSessionModel extends Model
{
    protected $table      = 'chat_sessions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id', 'servidor_id', 'titulo', 'tipo', 'status', 'protocol_number'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Retorna o histórico de sessões de um usuário específico, trazendo junto 
     * a última mensagem de forma otimizada para a barra lateral do chat.
     */
    public function getHistoricoComUltimaMensagem(int $userId): array
    {
        return $this->db->table('chat_sessions s')
            ->select('s.*, 
                (SELECT message FROM chat_messages WHERE session_id = s.id ORDER BY id DESC LIMIT 1) AS last_message,
                (SELECT created_at FROM chat_messages WHERE session_id = s.id ORDER BY id DESC LIMIT 1) AS last_at,
                (SELECT COUNT(*) FROM chat_messages WHERE session_id = s.id AND is_read = 0 AND sender_role != "mei") AS unread')
            ->where('s.user_id', $userId)
            ->orderBy('s.updated_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}