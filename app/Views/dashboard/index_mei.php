<div class="space-y-6" x-data="dashboardMei()">

  <!-- Saudação + alerta de pendências -->
  <div class="flex flex-col md:flex-row md:items-center gap-4">
    <div class="flex-1">
      <h1 class="text-xl font-bold text-[#031617]">
        Olá, <?= esc(explode(' ', session()->get('user_name'))[0]) ?>! 👋
      </h1>
      <p class="text-sm text-[#4a6b5a] mt-0.5">
        <?= esc(session()->get('user_nome_fantasia') ?? '') ?> ·
        <?= date('l, d \d\e F \d\e Y', strtotime('today')) ?>
      </p>
    </div>

    <?php if ($pending_docs > 0): ?>
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border"
      style="background:#fffbeb;border-color:#fde68a">
      <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg flex-shrink-0"></i>
      <div>
        <p class="text-xs font-semibold text-amber-800">
          <?= $pending_docs ?> pendência<?= $pending_docs > 1 ? 's' : '' ?> ativa<?= $pending_docs > 1 ? 's' : '' ?>
        </p>
        <p class="text-xs text-amber-700">Verifique documentos ou protocolos em aberto.</p>
      </div>
      <a href="<?= base_url('/portal') ?>"
        class="ml-2 px-3 py-1 rounded-lg text-xs font-semibold"
        style="background:#1A4B3D;color:#fff">Ver</a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Stats cards -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
    $cards = [
      ['icon'=>'file-earmark-text','label'=>'Protocolos Ativos',  'value'=> $status_count['Pendente'] + $status_count['Em Análise'], 'color'=>'#1A4B3D','bg'=>'#D6F2A6'],
      ['icon'=>'check-circle',     'label'=>'Docs Regulares',     'value'=> $regular_docs,  'color'=>'#16a34a','bg'=>'#dcfce7'],
      ['icon'=>'exclamation-circle','label'=>'Docs Pendentes',    'value'=> $pending_docs,  'color'=>'#d97706','bg'=>'#fef9c3'],
      ['icon'=>'check2-all',       'label'=>'Protocolos Resolvidos','value'=> $status_count['Resolvido'], 'color'=>'#0369a1','bg'=>'#e0f2fe'],
    ];
    foreach ($cards as $c): ?>
    <div class="bg-white rounded-2xl p-4 border" style="border-color:rgba(26,75,61,.1)">
      <div class="flex items-center justify-between mb-3">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center"
          style="background:<?= $c['bg'] ?>">
          <i class="bi bi-<?= $c['icon'] ?> text-base" style="color:<?= $c['color'] ?>"></i>
        </div>
      </div>
      <p class="text-2xl font-bold text-[#031617]"><?= $c['value'] ?></p>
      <p class="text-xs text-[#4a6b5a] mt-0.5"><?= $c['label'] ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Chart + Recent protocols -->
  <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

    <!-- Chart -->
    <div class="md:col-span-3 bg-white rounded-2xl p-5 border" style="border-color:rgba(26,75,61,.1)">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-sm font-semibold text-[#031617]">Atendimentos Mensais</h2>
          <p class="text-xs text-[#4a6b5a]">Sala do Empreendedor — Nova Lima (2025)</p>
        </div>
      </div>
      <canvas id="chartAtend" height="180"></canvas>
    </div>

    <!-- Recent protocols -->
    <div class="md:col-span-2 bg-white rounded-2xl p-5 border" style="border-color:rgba(26,75,61,.1)">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-[#031617]">Protocolos Recentes</h2>
        <a href="<?= base_url('/protocolos') ?>"
          class="text-xs text-[#1A4B3D] hover:underline font-medium">Ver todos</a>
      </div>

      <?php if (empty($recent_protocols)): ?>
      <div class="text-center py-8">
        <i class="bi bi-file-earmark-text text-3xl" style="color:#D6F2A6"></i>
        <p class="text-sm text-[#4a6b5a] mt-2">Nenhum protocolo ainda</p>
        <a href="<?= base_url('/portal') ?>"
          class="mt-3 inline-block px-4 py-2 rounded-xl text-xs font-semibold"
          style="background:#1A4B3D;color:#fff">Abrir protocolo</a>
      </div>
      <?php else: ?>
      <div class="space-y-3">
        <?php foreach ($recent_protocols as $p):
          $statusColor = match($p['status']) {
            'Resolvido'   => ['bg'=>'#dcfce7','text'=>'#16a34a'],
            'Em Análise'  => ['bg'=>'#dbeafe','text'=>'#1d4ed8'],
            default       => ['bg'=>'#fef9c3','text'=>'#b45309'],
          };
        ?>
        <div class="flex items-start gap-3 p-3 rounded-xl bg-[#f8fcf8] cursor-pointer
          hover:bg-[#f0f9f0] transition-colors"
          @click="openProtocol(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-[#031617] truncate"><?= esc($p['protocol_number']) ?></p>
            <p class="text-xs text-[#4a6b5a] truncate"><?= esc($p['descricao']) ?></p>
            <p class="text-[10px] text-[#4a6b5a] mt-0.5"><?= date('d/m/Y', strtotime($p['created_at'])) ?></p>
          </div>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap"
            style="background:<?= $statusColor['bg'] ?>;color:<?= $statusColor['text'] ?>">
            <?= $p['status'] ?>
          </span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal detalhe protocolo -->
  <div x-show="modal" x-cloak @click.self="modal=false"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" x-transition>
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[#031617]" x-text="sel.protocol_number"></h3>
        <button @click="modal=false" class="text-[#4a6b5a] hover:text-[#031617]">
          <i class="bi bi-x text-xl"></i>
        </button>
      </div>
      <div class="space-y-3 text-sm">
        <div class="flex justify-between">
          <span class="text-[#4a6b5a]">Status</span>
          <span class="font-semibold text-[#031617]" x-text="sel.status"></span>
        </div>
        <div class="flex justify-between">
          <span class="text-[#4a6b5a]">Canal</span>
          <span class="font-semibold" x-text="sel.canal"></span>
        </div>
        <div class="flex justify-between">
          <span class="text-[#4a6b5a]">Data</span>
          <span x-text="sel.created_at ? new Date(sel.created_at).toLocaleDateString('pt-BR') : ''"></span>
        </div>
        <div class="pt-2 border-t" style="border-color:rgba(26,75,61,.1)">
          <p class="text-[#4a6b5a] text-xs mb-1">Descrição</p>
          <p class="text-[#031617]" x-text="sel.descricao"></p>
        </div>
        <template x-if="sel.observacao">
          <div class="p-3 rounded-xl bg-[#f0f9f0] border" style="border-color:rgba(26,75,61,.12)">
            <p class="text-xs font-semibold text-[#1A4B3D] mb-1">Observação do servidor</p>
            <p class="text-xs text-[#031617]" x-text="sel.observacao"></p>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>

<script>
function dashboardMei() {
  return {
    modal: false,
    sel: {},
    openProtocol(p) { this.sel = p; this.modal = true; },
    init() {
      const data = <?= $chart_data ?>;
      const ctx  = document.getElementById('chartAtend');
      if (!ctx) return;
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.map(d => d.mes),
          datasets: [{
            label: 'Atendimentos',
            data: data.map(d => d.atendimentos),
            backgroundColor: 'rgba(214,242,166,.7)',
            borderColor: '#1A4B3D',
            borderWidth: 2,
            borderRadius: 8,
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(26,75,61,.06)' } },
            x: { grid: { display: false } }
          }
        }
      });
    }
  }
}
</script>
