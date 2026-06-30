<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';

    protected $allowedFields = ['user_id', 'title', 'description', 'is_read'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function markRead(int $id, int $userId): void
    {
        $this->where('id', $id)->where('user_id', $userId)
             ->set(['is_read' => 1])->update();
    }

    public function markAllRead(int $userId): void
    {
        $this->where('user_id', $userId)->set(['is_read' => 1])->update();
    }

    public function countUnread(int $userId): int
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }
}
