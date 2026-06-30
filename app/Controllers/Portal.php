<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\ProtocolModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Portal extends Controller
{
    public function index()
    {
        $userId = session()->get('user_id');
        $role   = session()->get('user_role');

        $notifModel    = new NotificationModel();
        $notifications = $notifModel->getByUser($userId);

        $user = [
            'id'           => $userId,
            'name'         => session()->get('user_name'),
            'email'        => session()->get('user_email'),
            'role'         => $role,
            'cnpj'         => session()->get('user_cnpj'),
            'nome_fantasia'=> session()->get('user_nome_fantasia'),
            'telefone'     => session()->get('user_telefone'),
            'atividade'    => session()->get('user_atividade'),
            'municipio'    => session()->get('user_municipio'),
            'situacao'     => session()->get('user_situacao'),
            'matricula'    => session()->get('user_matricula'),
        ];

        if ($role === 'mei') {
            $docModel      = new DocumentModel();
            $protocolModel = new ProtocolModel();

            $data = [
                'user'          => $user,
                'notifications' => $notifications,
                'documents'     => $docModel->getByUser($userId),
                'protocols'     => $protocolModel->getByUser($userId),
                'active_page'   => 'portal',
                'success'       => session()->getFlashdata('success'),
            ];

            $innerView = view('portal/mei', $data);
        } else {
            $userModel     = new UserModel();
            $protocolModel = new ProtocolModel();

            $data = [
                'user'          => $user,
                'notifications' => $notifications,
                'meis'          => $userModel->getMeis(),
                'protocols'     => $protocolModel->getAllWithUser(),
                'active_page'   => 'portal',
            ];

            $innerView = view('portal/server', $data);
        }

        return view('layouts/main', array_merge($data, ['page_content' => $innerView]));
    }

    // ─── AJAX: criar novo protocolo ───────────────────────────────────────────
    public function novoProtocolo()
    {
        $input    = $this->request->getJSON(true);
        $userId   = session()->get('user_id');

        $protocolModel = new ProtocolModel();
        $number = $protocolModel->generateNumber();

        $protocolModel->insert([
            'protocol_number' => $number,
            'user_id'         => $userId,
            'descricao'       => $input['descricao']  ?? 'Solicitação',
            'categoria'       => $input['categoria']  ?? 'Geral',
            'status'          => 'Em Análise',
            'canal'           => 'Portal',
            'observacao'      => $input['observacao'] ?? null,
        ]);

        return $this->response->setJSON(['ok' => true, 'numero' => $number]);
    }
}
