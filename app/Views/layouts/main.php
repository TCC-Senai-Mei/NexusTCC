<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal Nexus — Sala do Empreendedor Nova Lima</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<style>
  [x-cloak]{display:none!important}
  ::-webkit-scrollbar{width:5px}
  ::-webkit-scrollbar-track{background:#f2f8f2}
  ::-webkit-scrollbar-thumb{background:rgba(26,75,61,.3);border-radius:5px}
  .sidebar-link{transition:background .15s,color .15s}
  .sidebar-link.active{background:#f0f9f0;color:#1A4B3D;font-weight:600}
  .sidebar-link:not(.active):hover{background:#f5faf5}
  .badge-dot{animation:pulse 2s infinite}
  @keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}
  .markdown strong{font-weight:700}
  .markdown em{font-style:italic}
</style>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary:   '#1A4B3D',
          secondary: '#D6F2A6',
          fore:      '#031617',
          muted:     '#4a6b5a',
        }
      }
    }
  }
</script>
</head>

<body class="bg-gray-50 text-[#031617] font-sans"
  x-data="nexusApp()"
  x-cloak>

<?php
  $userId    = session()->get('user_id');
  $userName  = session()->get('user_name');
  $userRole  = session()->get('user_role');
  $initials  = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $userName), 0, 2)));
  $isMei     = $userRole === 'mei';
  $ap        = $active_page ?? 'dashboard';

  $navMei = [
    ['icon'=>'speedometer2',   'label'=>'Dashboard',      'page'=>'dashboard',    'href'=>'/dashboard'],
    ['icon'=>'building',       'label'=>'Meu Portal MEI', 'page'=>'portal',       'href'=>'/portal'],
    ['icon'=>'chat-square-text','label'=>'Assistente',    'page'=>'chat',         'href'=>'/chat'],
    ['icon'=>'file-text',      'label'=>'Meus Protocolos','page'=>'protocolos',   'href'=>'/protocolos'],
    ['icon'=>'gear',           'label'=>'Configurações',  'page'=>'configuracoes','href'=>'/configuracoes'],
  ];
  $navServidor = [
    ['icon'=>'speedometer2',   'label'=>'Dashboard',         'page'=>'dashboard',    'href'=>'/dashboard'],
    ['icon'=>'building',       'label'=>'Painel de Atend.',  'page'=>'portal',       'href'=>'/portal'],
    ['icon'=>'chat-square-text','label'=>'Assistente',       'page'=>'chat',         'href'=>'/chat'],
    ['icon'=>'file-text',      'label'=>'Protocolos Ativos', 'page'=>'protocolos',   'href'=>'/protocolos'],
    ['icon'=>'gear',           'label'=>'Configurações',     'page'=>'configuracoes','href'=>'/configuracoes'],
  ];
  $nav = $isMei ? $navMei : $navServidor;

  $notifs       = $notifications ?? [];
  $unreadCount  = count(array_filter($notifs, fn($n) => !$n['is_read']));
?>

<!-- ─── SIDEBAR ───────────────────────────────────────────────────── -->
<aside class="fixed inset-y-0 left-0 z-40 flex flex-col bg-white border-r transition-all duration-200"
  style="border-color:rgba(26,75,61,.12);"
  :class="collapsed ? 'w-[60px]' : 'w-[220px]'">

  <!-- Logo -->
  <div class="flex items-center gap-2.5 px-4 py-5 border-b flex-shrink-0" style="border-color:rgba(26,75,61,.1)">
    <div class="flex-shrink-0">
      <svg width="28" height="28" viewBox="0 0 36 36" fill="none">
        <rect x="2" y="12" width="14" height="14" rx="2" fill="#D6F2A6"/>
        <rect x="10" y="4" width="14" height="14" rx="2" fill="#1A4B3D"/>
        <rect x="20" y="18" width="14" height="14" rx="2" fill="#2d7a5f"/>
      </svg>
    </div>
    <div x-show="!collapsed" x-transition.opacity class="overflow-hidden">
      <p class="font-bold text-[#1A4B3D] text-lg leading-tight" style="font-family:system-ui">nexus</p>
      <p class="text-[10px] text-[#4a6b5a] leading-tight whitespace-nowrap">Nova Lima — MEI</p>
    </div>
  </div>

  <!-- Nav items -->
  <nav class="flex-1 py-3 space-y-0.5 overflow-y-auto px-2">
    <?php foreach ($nav as $item): ?>
    <a href="<?= base_url($item['href']) ?>"
      class="sidebar-link flex items-center gap-3 px-2.5 py-2.5 rounded-xl text-sm <?= $ap === $item['page'] ? 'active' : 'text-[#4a6b5a]' ?>">
      <i class="bi bi-<?= $item['icon'] ?> text-lg flex-shrink-0"></i>
      <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap"><?= $item['label'] ?></span>
    </a>
    <?php endforeach; ?>
  </nav>

  <!-- Collapse button -->
  <div class="p-2 border-t" style="border-color:rgba(26,75,61,.1)">
    <button @click="collapsed=!collapsed"
      class="w-full flex items-center justify-center gap-2 px-2 py-2 rounded-xl text-xs text-[#4a6b5a] hover:bg-gray-50 transition-colors">
      <i class="bi" :class="collapsed ? 'bi-chevron-double-right' : 'bi-chevron-double-left'"></i>
      <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Recolher</span>
    </button>
  </div>
</aside>

<!-- ─── MAIN WRAPPER ─────────────────────────────────────────────── -->
<div class="flex flex-col min-h-screen transition-all duration-200"
  :style="collapsed ? 'margin-left:60px' : 'margin-left:220px'">

  <!-- HEADER -->
  <header class="sticky top-0 z-30 bg-white border-b flex items-center gap-3 px-5 py-3"
    style="border-color:rgba(26,75,61,.1)">

    <!-- Search -->
    <div class="relative flex-1 max-w-xs hidden md:block">
      <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-[#4a6b5a] text-sm"></i>
      <input type="text" placeholder="Buscar..."
        class="w-full pl-9 pr-4 py-1.5 rounded-xl text-sm bg-[#f8fcf8] border outline-none focus:border-[#1A4B3D] transition-colors"
        style="border-color:rgba(26,75,61,.15)">
    </div>

    <div class="flex items-center gap-2 ml-auto">

      <!-- Notificações -->
      <div class="relative" x-ref="notifPanel">
        <button @click="notifOpen=!notifOpen"
          class="relative p-2 rounded-xl hover:bg-gray-50 transition-colors">
          <i class="bi bi-bell text-[#1A4B3D] text-xl"></i>
          <?php if ($unreadCount > 0): ?>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full badge-dot"></span>
          <?php endif; ?>
        </button>

        <!-- Notification dropdown -->
        <div x-show="notifOpen" x-cloak @click.away="notifOpen=false"
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-lg border overflow-hidden z-50"
          style="border-color:rgba(26,75,61,.12)">
          <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color:rgba(26,75,61,.08)">
            <span class="text-sm font-semibold text-[#031617]">Notificações</span>
            <?php if ($unreadCount > 0): ?>
            <button @click="markAllRead()"
              class="text-xs text-[#1A4B3D] hover:underline font-medium">Marcar todas lidas</button>
            <?php endif; ?>
          </div>
          <div class="max-h-80 overflow-y-auto divide-y" style="divide-color:rgba(26,75,61,.06)">
            <?php if (empty($notifs)): ?>
            <p class="text-sm text-[#4a6b5a] text-center py-8">Nenhuma notificação</p>
            <?php else: foreach ($notifs as $n): ?>
            <div class="flex items-start gap-3 px-4 py-3 cursor-pointer hover:bg-[#f8fcf8] transition-colors <?= !$n['is_read'] ? 'bg-[#f0f9f0]' : '' ?>"
              @click="markRead(<?= $n['id'] ?>, $el)">
              <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 <?= !$n['is_read'] ? 'bg-[#1A4B3D]' : 'bg-transparent' ?>"></div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-[#031617] truncate"><?= esc($n['title']) ?></p>
                <p class="text-xs text-[#4a6b5a] mt-0.5 truncate"><?= esc($n['description']) ?></p>
                <p class="text-[10px] text-[#4a6b5a] mt-1"><?= date('d/m H:i', strtotime($n['created_at'])) ?></p>
              </div>
            </div>
            <?php endforeach; endif; ?>
          </div>
        </div>
      </div>

      <!-- User avatar / menu -->
      <div class="relative">
        <button @click="userMenuOpen=!userMenuOpen"
          class="flex items-center gap-2 hover:opacity-80 transition-opacity">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
            style="background:#1A4B3D;color:#D6F2A6">
            <?= $initials ?>
          </div>
          <div class="hidden md:block text-left">
            <p class="text-xs font-semibold text-[#031617] leading-tight max-w-[120px] truncate"><?= esc($userName) ?></p>
            <p class="text-[10px] text-[#4a6b5a] leading-tight"><?= $isMei ? 'MEI' : 'Servidor' ?></p>
          </div>
          <i class="bi bi-chevron-down text-xs text-[#4a6b5a] hidden md:block"></i>
        </button>

        <div x-show="userMenuOpen" x-cloak @click.away="userMenuOpen=false"
          x-transition:enter="transition ease-out duration-100"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border z-50 overflow-hidden"
          style="border-color:rgba(26,75,61,.12)">
          <a href="<?= base_url('/configuracoes') ?>"
            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#031617] hover:bg-[#f8fcf8]">
            <i class="bi bi-person text-[#4a6b5a]"></i> Perfil
          </a>
          <a href="<?= base_url('/configuracoes') ?>"
            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#031617] hover:bg-[#f8fcf8]">
            <i class="bi bi-gear text-[#4a6b5a]"></i> Configurações
          </a>
          <div class="border-t" style="border-color:rgba(26,75,61,.08)"></div>
          <a href="<?= base_url('/logout') ?>"
            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
            <i class="bi bi-box-arrow-right"></i> Sair
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- PAGE CONTENT -->
  <main class="flex-1 p-6">
    <?= $page_content ?? '' ?>
  </main>
</div>

<script>
function nexusApp() {
  return {
    collapsed:    false,
    notifOpen:    false,
    userMenuOpen: false,

    markRead(id, el) {
      // Marca visualmente
      el.querySelector('.rounded-full.bg-\\[\\#1A4B3D\\]')?.classList.replace('bg-[#1A4B3D]','bg-transparent');
      el.classList.remove('bg-[#f0f9f0]');
      // AJAX
      fetch('<?= base_url('/api/notif/') ?>' + id).catch(() => {});
    },

    markAllRead() {
      document.querySelectorAll('[x-ref="notifPanel"] .rounded-full').forEach(d => {
        d.classList.replace('bg-[#1A4B3D]','bg-transparent');
      });
      document.querySelectorAll('[x-ref="notifPanel"] .bg-\\[\\#f0f9f0\\]').forEach(d => {
        d.classList.remove('bg-[#f0f9f0]');
      });
      fetch('<?= base_url('/api/notif/todas') ?>').catch(() => {});
      this.notifOpen = false;
    }
  }
}
</script>
</body>
</html>
