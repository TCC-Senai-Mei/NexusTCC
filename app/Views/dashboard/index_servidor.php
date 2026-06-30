<div class="space-y-6" x-data="dashboardServidor()">

  <!-- Header -->
  <div>
    <h1 class="text-xl font-bold text-[#031617]">
      Painel Gerencial — <?= esc(explode(' ', session()->get('user_name'))[0]) ?>
    </h1>
    <p class="text-sm text-[#4a6b5a] mt-0.5">Sala do Empreendedor · Nova Lima · Visão geral dos atendimentos</p>
  </div>

  <!-- Stats cards -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
    $totalProtocols = array_sum($status_count);
    $resolucao = $totalProtocols > 0 ? round(($status_count['Resolvido'] / $totalProtocols) * 100) : 0;
    $cards = [
      ['icon'=>'people',           'label'=>'Total de MEIs',       'value'=> $total_meis,               'color'=>'#1A4B3D','bg'=>'#D6F2A6'],
      ['icon'=>'file-earmark-plus','label'=>'Protocolos Ativos',   'value'=> $active_pending,           'color'=>'#d97706','bg'=>'#fef9c3'],
      ['icon'=>'check2-all',       'label'=>'Protocolos Resolvidos','value'=> $status_count['Resolvido'],'color'=>'#16a34a','bg'=>'#dcfce7'],
      ['icon'=>'graph-up-arrow',   'label'=>'Taxa de Resolução',   'value'=> $resolucao.'%',            'color'=>'#0369a1','bg'=>'#e0f2fe'],
    ];
    foreach ($cards as $c): ?>
    <div class="bg-white rounded-2xl p-4 border" style="border-color:rgba(26,75,61,.1)">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3"
        style="background:<?= $c['bg'] ?>">
        <i class="bi bi-<?= $c['icon'] ?> text-base" style="color:<?= $c['color'] ?>"></i>
      </div>
      <p class="text-2xl font-bold text-[#031617]"><?= $c['value'] ?></p>
      <p class="text-xs text-[#4a6b5a] mt-0.5"><?= $c['label'] ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Chart + Protocolos recentes -->
  <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

    <div class="md:col-span-3 bg-white rounded-2xl p-5 border" style="border-color:rgba(26,75,61,.1)">
      <h2 class="text-sm font-semibold text-[#031617] mb-1">Atendimentos Mensais</h2>
      <p class="text-xs text-[#4a6b5a] mb-4">Total de protocolos abertos por mês (2025)</p>
      <canvas id="chartServidor" height="180"></canvas>
    </div>

    <div class="md:col-span-2 bg-white rounded-2xl p-5 border" style="border-color:rgba(26,75,61,.1)">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-[#031617]">Protocolos Pendentes</h2>
        <a href="<?= base_url('/protocolos') ?>"
          class="text-xs text-[#1A4B3D] hover:underline font-medium">Ver todos</a>
      </div>
      <?php
      $pending = array_filter($all_protocols, fn($p) => $p['status'] !== 'Resolvido');
      $pending = array_slice($pending, 0, 4);
      ?>
      <?php if (empty($pending)): ?>
      <p class="text-sm text-[#4a6b5a] text-center py-8">Nenhum protocolo pendente</p>
      <?php else: ?>
      <div class="space-y-3">
        <?php foreach ($pending as $p):
          $sc = match($p['status']) {
            'Em Análise' => ['bg'=>'#dbeafe','text'=>'#1d4ed8'],
            default      => ['bg'=>'#fef9c3','text'=>'#b45309'],
          };
        ?>
        <div class="flex items-start gap-3 p-3 rounded-xl bg-[#f8fcf8] hover:bg-[#f0f9f0] transition-colors cursor-pointer"
          @click="openProtocol(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-[#031617] truncate"><?= esc($p['protocol_number']) ?></p>
            <p class="text-xs text-[#4a6b5a] truncate"><?= esc($p['mei_nome'] ?? '') ?></p>
            <p class="text-[10px] text-[#4a6b5a]"><?= date('d/m/Y', strtotime($p['created_at'])) ?></p>
          </div>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap"
            style="background:<?= $sc['bg'] ?>;color:<?= $sc['text'] ?>">
            <?= $p['status'] ?>
          </span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tabela MEIs cadastrados -->
  <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(26,75,61,.08)">
      <h2 class="text-sm font-semibold text-[#031617]">MEIs Cadastrados</h2>
      <span class="text-xs text-[#4a6b5a]"><?= $total_meis ?> empreendedor<?= $total_meis !== 1 ? 'es' : '' ?></span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-xs">
        <thead class="bg-[#f8fcf8]">
          <tr>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Nome / Empresa</th>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">CNPJ</th>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Atividade</th>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Situação</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y" style="divide-color:rgba(26,75,61,.06)">
          <?php foreach ($meis as $m): ?>
          <tr class="hover:bg-[#f8fcf8] transition-colors">
            <td class="px-5 py-3">
              <p class="font-semibold text-[#031617]"><?= esc($m['nome_fantasia'] ?: $m['name']) ?></p>
              <p class="text-[10px] text-[#4a6b5a]"><?= esc($m['name']) ?></p>
            </td>
            <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell"><?= esc($m['cnpj']) ?></td>
            <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell truncate max-w-[150px]"><?= esc($m['atividade']) ?></td>
            <td class="px-5 py-3">
              <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                style="background:#dcfce7;color:#16a34a">Regular</span>
            </td>
            <td class="px-5 py-3">
              <a href="<?= base_url('/portal') ?>"
                class="text-[#1A4B3D] hover:underline font-medium">Ver</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal protocolo -->
  <div x-show="modal" x-cloak @click.self="modal=false"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" x-transition>
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[#031617]" x-text="sel.protocol_number"></h3>
        <button @click="modal=false"><i class="bi bi-x text-xl text-[#4a6b5a]"></i></button>
      </div>
      <div class="space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-[#4a6b5a]">MEI</span><span class="font-semibold" x-text="sel.mei_nome"></span></div>
        <div class="flex justify-between"><span class="text-[#4a6b5a]">Status</span><span class="font-semibold" x-text="sel.status"></span></div>
        <div class="flex justify-between"><span class="text-[#4a6b5a]">Categoria</span><span x-text="sel.categoria"></span></div>
        <div class="flex justify-between"><span class="text-[#4a6b5a]">Canal</span><span x-text="sel.canal"></span></div>
        <div class="pt-2 border-t" style="border-color:rgba(26,75,61,.1)">
          <p class="text-[#4a6b5a] text-xs mb-1">Descrição</p>
          <p x-text="sel.descricao"></p>
        </div>
      </div>
      <a href="<?= base_url('/protocolos') ?>"
        class="mt-4 w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold"
        style="background:#1A4B3D;color:#fff">
        Gerenciar protocolo <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>
</div>

<script>
function dashboardServidor() {
  return {
    modal: false, sel: {},
    openProtocol(p) { this.sel = p; this.modal = true; },
    init() {
      const data = <?= $chart_data ?>;
      const ctx  = document.getElementById('chartServidor');
      if (!ctx) return;
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.map(d => d.mes),
          datasets: [{
            label: 'Protocolos',
            data: data.map(d => d.total),
            fill: true,
            backgroundColor: 'rgba(214,242,166,.25)',
            borderColor: '#1A4B3D',
            borderWidth: 2.5,
            tension: 0.4,
            pointBackgroundColor: '#1A4B3D',
            pointRadius: 4,
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
