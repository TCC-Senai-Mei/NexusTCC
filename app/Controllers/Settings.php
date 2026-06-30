<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Settings extends Controller
{
    public function index()
    {
        $userId = session()->get('user_id');
        $notifModel = new NotificationModel();

        $user = [
            'id'           => $userId,
            'name'         => session()->get('user_name'),
            'email'        => session()->get('user_email'),
            'role'         => session()->get('user_role'),
            'cnpj'         => session()->get('user_cnpj'),
            'nome_fantasia'=> session()->get('user_nome_fantasia'),
            'telefone'     => session()->get('user_telefone'),
            'atividade'    => session()->get('user_atividade'),
            'matricula'    => session()->get('user_matricula'),
        ];

        $data = [
            'user'          => $user,
            'notifications' => $notifModel->getByUser($userId),
            'active_page'   => 'configuracoes',
            'success'       => session()->getFlashdata('settings_success'),
        ];

        $innerView = view('settings/index', $data);
        return view('layouts/main', array_merge($data, ['page_content' => $innerView]));
    }

    public function salvar()
    {
        $userId    = session()->get('user_id');
        $userModel = new UserModel();

        $update = [
            'name'     => $this->request->getPost('nome'),
            'email'    => $this->request->getPost('email'),
            'telefone' => $this->request->getPost('telefone'),
        ];

        // Atualiza senha se fornecida
        $novaSenha = $this->request->getPost('nova_senha');
        if ($novaSenha && strlen($novaSenha) >= 6) {
            $senhaAtual = $this->request->getPost('senha_atual');
            $user = $userModel->find($userId);
            if ($user && $user['password'] === $senhaAtual) {
                $update['password'] = $novaSenha;
            }
        }

        $userModel->update($userId, $update);

        // Atualiza sessão
        session()->set('user_name',  $update['name']);
        session()->set('user_email', $update['email']);

        return redirect()->to(base_url('/configuracoes'))
                         ->with('settings_success', 'Alterações salvas com sucesso!');
    }
}
