<?php

namespace App\Controllers;

use App\Models\ProtocolModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Protocols extends Controller
{
    public function index()
    {
        $userId   = session()->get('user_id');
        $role     = session()->get('user_role');

        $protocolModel = new ProtocolModel();
        $notifModel    = new NotificationModel();

        $protocols = ($role === 'servidor')
            ? $protocolModel->getAllWithUser()
            : $protocolModel->getByUser($userId);

        $notifications = $notifModel->getByUser($userId);

        $user = [
            'id'   => $userId,
            'name' => session()->get('user_name'),
            'role' => $role,
        ];

        $data = [
            'user'          => $user,
            'notifications' => $notifications,
            'protocols'     => $protocols,
            'active_page'   => 'protocolos',
        ];

        $innerView = view('protocols/index', $data);
        return view('layouts/main', array_merge($data, ['page_content' => $innerView]));
    }
}
