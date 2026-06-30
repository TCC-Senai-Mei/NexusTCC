<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name', 'email', 'password', 'role',
        'cnpj', 'nome_fantasia', 'telefone', 'atividade',
        'matricula', 'municipio', 'situacao',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];

    // ─── Autenticação MEI ────────────────────────────────────────────────────
    public function findByCnpj(string $cnpj): ?array
    {
        return $this->where('cnpj', $cnpj)->where('role', 'mei')->first();
    }

    // ─── Autenticação Servidor ───────────────────────────────────────────────
    public function findByMatricula(string $matricula): ?array
    {
        return $this->where('matricula', $matricula)->where('role', 'servidor')->first();
    }

    // ─── Listagem de MEIs para o painel do servidor ──────────────────────────
    public function getMeis(): array
    {
        return $this->where('role', 'mei')->orderBy('name', 'ASC')->findAll();
    }
}
