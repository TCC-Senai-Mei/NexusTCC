<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ─── Rotas públicas ───────────────────────────────────────────────────────────
$routes->get('/',              'Auth::index');
$routes->get('/login',         'Auth::index');
$routes->post('/auth/mei',     'Auth::meiLogin');
$routes->post('/auth/servidor','Auth::servidorLogin');
$routes->post('/auth/cadastro','Auth::cadastro');
$routes->get('/logout',        'Auth::logout');

// ─── Rotas protegidas (requerem sessão) ───────────────────────────────────────
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/dashboard',       'Dashboard::index');
    $routes->get('/portal',          'Portal::index');
    $routes->get('/protocolos',      'Protocols::index');
    $routes->get('/configuracoes',   'Settings::index');
    $routes->post('/configuracoes',  'Settings::salvar');

    // ─── NOVO CHATBOT REWORK (Controlador Isolado) ─────────────────────────
    $routes->get('/chat',                      'ChatController::index');
    $routes->get('/chat/listarSessoes',        'ChatController::listarSessoes');
    $routes->post('/chat/criarSessao',         'ChatController::criarSessao');
    $routes->get('/chat/carregarMensagens/(:num)', 'ChatController::carregarMensagens/$1');
    $routes->post('/chat/enviarMensagem',      'ChatController::enviarMensagem');

    // ─── API (AJAX) Legada / Antiga ────────────────────────────────────────
    $routes->post('/api/chat/nova',            'Chat::novaConversa');
    $routes->get('/api/chat/mensagens/(:num)', 'Chat::mensagens/$1');
    $routes->post('/api/chat/poll/(:num)',      'Chat::poll/$1');
    $routes->post('/api/chat/resposta',        'Chat::resposta');
    $routes->post('/api/chat/servidor',        'Chat::responderServidor');
    $routes->post('/api/protocolo',            'Portal::novoProtocolo');
    $routes->get('/api/notif/(:num)',          'Dashboard::marcarLida/$1');
});