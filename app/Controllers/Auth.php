<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('/dashboard'));
        }
        return view('auth/login', [
            'error'          => session()->getFlashdata('error'),
            'signup_error'   => session()->getFlashdata('signup_error'),
            'signup_success' => session()->getFlashdata('signup_success'),
        ]);
    }

    // ─── Login MEI ────────────────────────────────────────────────────────────
    public function meiLogin()
    {
        $cnpj  = $this->request->getPost('cnpj');
        $senha = $this->request->getPost('senha');

        $userModel = new UserModel();
        $user = $userModel->findByCnpj($cnpj);

        if ($user && $user['password'] === $senha) {
            session()->set([
                'logged_in'         => true,
                'user_id'           => $user['id'],
                'user_name'         => $user['name'],
                'user_email'        => $user['email'],
                'user_role'         => $user['role'],
                'user_cnpj'         => $user['cnpj'],
                'user_nome_fantasia'=> $user['nome_fantasia'],
                'user_telefone'     => $user['telefone'],
                'user_atividade'    => $user['atividade'],
                'user_municipio'    => $user['municipio'],
                'user_situacao'     => $user['situacao'],
            ]);
            return redirect()->to(base_url('/dashboard'));
        }

        return redirect()->to(base_url('/login'))
                         ->with('error', 'CNPJ ou senha incorretos. Tente novamente.');
    }

    // ─── Login Servidor ───────────────────────────────────────────────────────
    public function servidorLogin()
    {
        $matricula = $this->request->getPost('matricula');
        $senha     = $this->request->getPost('senha');

        $userModel = new UserModel();
        $user = $userModel->findByMatricula($matricula);

        if ($user && $user['password'] === $senha) {
            session()->set([
                'logged_in'      => true,
                'user_id'        => $user['id'],
                'user_name'      => $user['name'],
                'user_email'     => $user['email'],
                'user_role'      => $user['role'],
                'user_matricula' => $user['matricula'],
            ]);
            return redirect()->to(base_url('/dashboard'));
        }

        return redirect()->to(base_url('/login'))
                         ->with('error', 'Matrícula ou senha incorretos. Tente novamente.');
    }

    // ─── Cadastro MEI ─────────────────────────────────────────────────────────
    public function cadastro()
    {
        $userModel = new UserModel();
        $cnpj      = $this->request->getPost('cnpj');

        if ($userModel->where('cnpj', $cnpj)->first()) {
            return redirect()->to(base_url('/login'))
                             ->with('signup_error', 'CNPJ já cadastrado no sistema.');
        }

        $senha = $this->request->getPost('senha');
        $conf  = $this->request->getPost('senha_conf');

        if ($senha !== $conf) {
            return redirect()->to(base_url('/login'))
                             ->with('signup_error', 'As senhas não coincidem.');
        }

        $userModel->insert([
            'name'         => $this->request->getPost('nome'),
            'email'        => $this->request->getPost('email'),
            'password'     => $senha,
            'role'         => 'mei',
            'cnpj'         => $cnpj,
            'nome_fantasia'=> $this->request->getPost('nome_fantasia'),
            'telefone'     => $this->request->getPost('telefone'),
            'atividade'    => $this->request->getPost('atividade'),
        ]);

        return redirect()->to(base_url('/login'))
                         ->with('signup_success', 'Cadastro realizado! Agora faça o login com seu CNPJ.');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}
