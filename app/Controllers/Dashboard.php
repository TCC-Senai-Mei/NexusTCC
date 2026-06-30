<?php

namespace App\Controllers;

use App\Models\ProtocolModel;
use App\Models\DocumentModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    private function getSessionUser(): array
    {
        return [
            'id'           => session()->get('user_id'),
            'name'         => session()->get('user_name'),
            'email'        => session()->get('user_email'),
            'role'         => session()->get('user_role'),
            'cnpj'         => session()->get('user_cnpj'),
            'nome_fantasia'=> session()->get('user_nome_fantasia'),
            'telefone'     => session()->get('user_telefone'),
            'atividade'    => session()->get('user_atividade'),
            'municipio'    => session()->get('user_municipio'),
            'situacao'     => session()->get('user_situacao'),
            'matricula'    => session()->get('user_matricula'),
        ];
    }

    public function index()
    {
        $user   = $this->getSessionUser();
        $userId = $user['id'];

        $protocolModel      = new ProtocolModel();
        $notificationModel  = new NotificationModel();
        $documentModel      = new DocumentModel();
        $userModel          = new UserModel();

        $notifications = $notificationModel->getByUser($userId);
        $statusCount   = $protocolModel->countByStatus($userId);

        // Dados específicos por papel
        if ($user['role'] === 'mei') {
            $recentProtocols = $protocolModel->getRecent($userId, 4);
            $documents       = $documentModel->getByUser($userId);

            $pendingDocs     = array_filter($documents, fn($d) => in_array($d['status'], ['Atrasado','Pendente de Envio']));
            $regularDocs     = array_filter($documents, fn($d) => $d['status'] === 'Regular');

            $chartData = [
                ['mes' => 'Jan', 'atendimentos' => 89],
                ['mes' => 'Fev', 'atendimentos' => 112],
                ['mes' => 'Mar', 'atendimentos' => 98],
                ['mes' => 'Abr', 'atendimentos' => 134],
                ['mes' => 'Mai', 'atendimentos' => 156],
                ['mes' => 'Jun', 'atendimentos' => 143],
            ];

            $contentData = [
                'user'            => $user,
                'notifications'   => $notifications,
                'recent_protocols'=> $recentProtocols,
                'documents'       => $documents,
                'pending_docs'    => count($pendingDocs),
                'regular_docs'    => count($regularDocs),
                'status_count'    => $statusCount,
                'chart_data'      => json_encode($chartData),
                'active_page'     => 'dashboard',
            ];

        } else {
            // Servidor — painel gerencial
            $allProtocols    = $protocolModel->getAllWithUser();
            $meis            = $userModel->getMeis();
            $totalMeis       = count($meis);
            $activePending   = array_filter($allProtocols, fn($p) => $p['status'] !== 'Resolvido');

            $chartData = [
                ['mes' => 'Jan', 'total' => 38],
                ['mes' => 'Fev', 'total' => 52],
                ['mes' => 'Mar', 'total' => 61],
                ['mes' => 'Abr', 'total' => 45],
                ['mes' => 'Mai', 'total' => 73],
                ['mes' => 'Jun', 'total' => 58],
            ];

            $contentData = [
                'user'          => $user,
                'notifications' => $notifications,
                'all_protocols' => $allProtocols,
                'meis'          => $meis,
                'total_meis'    => $totalMeis,
                'active_pending'=> count($activePending),
                'status_count'  => $protocolModel->countByStatus(),
                'chart_data'    => json_encode($chartData),
                'active_page'   => 'dashboard',
            ];
        }

        $innerView = ($user['role'] === 'mei')
            ? view('dashboard/index_mei', $contentData)
            : view('dashboard/index_servidor', $contentData);

        return view('layouts/main', array_merge($contentData, ['page_content' => $innerView]));
    }

    // ─── AJAX: marcar notificação como lida ───────────────────────────────────
    public function marcarLida(int $id)
    {
        $userId = session()->get('user_id');
        $model  = new NotificationModel();
        $model->markRead($id, $userId);
        return $this->response->setJSON(['ok' => true]);
    }

    // ─── AJAX: marcar todas como lidas ────────────────────────────────────────
    public function marcarTodasLidas()
    {
        $userId = session()->get('user_id');
        $model  = new NotificationModel();
        $model->markAllRead($userId);
        return $this->response->setJSON(['ok' => true]);
    }
}
