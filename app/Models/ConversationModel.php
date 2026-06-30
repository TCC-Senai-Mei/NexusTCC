<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversationModel extends Model
{
    protected $table      = 'conversations';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id', 'servidor_id', 'titulo', 'tipo',
        'status', 'protocol_number',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Todas as conversas de um MEI com a última mensagem
    public function getByUser(int $userId): array
    {
        return $this->db->table('conversations c')
            ->select('c.*, 
                (SELECT message FROM chat_messages WHERE conversation_id = c.id ORDER BY id DESC LIMIT 1) AS last_message,
                (SELECT created_at FROM chat_messages WHERE conversation_id = c.id ORDER BY id DESC LIMIT 1) AS last_at,
                (SELECT COUNT(*) FROM chat_messages WHERE conversation_id = c.id AND is_read = 0 AND sender_role != "mei") AS unread')
            ->where('c.user_id', $userId)
            ->orderBy('c.updated_at', 'DESC')
            ->get()->getResultArray();
    }

    // Conversas humanas pendentes (para o servidor)
    public function getHumanConversations(): array
    {
        return $this->db->table('conversations c')
            ->select('c.*, u.name AS mei_nome, u.nome_fantasia, u.cnpj,
                (SELECT message FROM chat_messages WHERE conversation_id = c.id ORDER BY id DESC LIMIT 1) AS last_message,
                (SELECT created_at FROM chat_messages WHERE conversation_id = c.id ORDER BY id DESC LIMIT 1) AS last_at,
                (SELECT COUNT(*) FROM chat_messages WHERE conversation_id = c.id AND is_read = 0 AND sender_role = "mei") AS unread')
            ->join('users u', 'u.id = c.user_id')
            ->where('c.tipo', 'humano')
            ->whereIn('c.status', ['aguardando_servidor', 'em_atendimento'])
            ->orderBy('c.updated_at', 'DESC')
            ->get()->getResultArray();
    }

    // Marca conversa como aguardando servidor + gera protocolo
    public function escalarParaHumano(int $id, string $protocolNumber): void
    {
        $this->update($id, [
            'tipo'           => 'humano',
            'status'         => 'aguardando_servidor',
            'protocol_number'=> $protocolNumber,
        ]);
    }
}
