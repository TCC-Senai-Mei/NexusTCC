<?php

namespace App\Models;

use CodeIgniter\Model;

class ProtocolModel extends Model
{
    protected $table      = 'protocols';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'protocol_number', 'user_id', 'descricao', 'categoria',
        'status', 'canal', 'servidor_id', 'observacao',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ─── Protocolos de um MEI específico ─────────────────────────────────────
    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    // ─── Todos os protocolos (painel servidor) ────────────────────────────────
    public function getAllWithUser(): array
    {
        return $this->db->table('protocols p')
            ->select('p.*, u.name AS mei_nome, u.nome_fantasia, u.cnpj')
            ->join('users u', 'u.id = p.user_id')
            ->orderBy('p.created_at', 'DESC')
            ->get()->getResultArray();
    }

    // ─── Gera número de protocolo único ──────────────────────────────────────
    public function generateNumber(): string
    {
        $year = date('Y');
        $last = $this->db->table('protocols')
            ->selectMax('id')
            ->get()->getRow();
        $next = ($last->id ?? 0) + 1;
        return 'NL-' . $year . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    // ─── Contagem por status ──────────────────────────────────────────────────
    public function countByStatus(int $userId = null): array
    {
        $builder = $this->db->table('protocols');
        if ($userId) {
            $builder->where('user_id', $userId);
        }
        $rows = $builder->select('status, COUNT(*) as total')
            ->groupBy('status')->get()->getResultArray();
        $result = ['Pendente' => 0, 'Em Análise' => 0, 'Resolvido' => 0];
        foreach ($rows as $row) {
            $result[$row['status']] = (int) $row['total'];
        }
        return $result;
    }

    // ─── Últimos 3 protocolos de um usuário ──────────────────────────────────
    public function getRecent(int $userId, int $limit = 3): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
