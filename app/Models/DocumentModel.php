<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table      = 'documents';
    protected $primaryKey = 'id';

    protected $allowedFields = ['user_id', 'tipo', 'status', 'vencimento'];

    protected $useTimestamps = false;

    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }
}
