<div class="space-y-5" x-data="protocolsPage()">

  <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-[#031617]">
        <?= session()->get('user_role') === 'servidor' ? 'Protocolos Ativos' : 'Meus Protocolos' ?>
      </h1>
      <p class="text-sm text-[#4a6b5a] mt-0.5">
        <?= session()->get('user_role') === 'servidor'
          ? 'Todos os protocolos do sistema'
          : 'Histórico completo dos seus atendimentos' ?>
      </p>
    </div>
    <?php if (session()->get('user_role') === 'mei'): ?>
    <a href="<?= base_url('/portal') ?>"
      class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold w-fit"
      style="background:#1A4B3D;color:#fff">
      <i class="bi bi-plus-circle"></i> Novo Protocolo
    </a>
    <?php endif; ?>
  </div>

  <!-- Filtros -->
  <div class="bg-white rounded-2xl p-4 border" style="border-color:rgba(26,75,61,.1)">
    <div class="flex flex-col md:flex-row gap-3">
      <div class="relative flex-1">
        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-[#4a6b5a] text-xs"></i>
        <input type="text" x-model="search" placeholder="Buscar por número ou descrição..."
          class="w-full pl-9 pr-4 py-2 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
          style="border-color:rgba(26,75,61,.15)">
      </div>
      <select x-model="filterStatus"
        class="px-3 py-2 rounded-xl text-sm border outline-none bg-white"
        style="border-color:rgba(26,75,61,.15)">
        <option value="">Todos os status</option>
        <option>Pendente</option>
        <option>Em Análise</option>
        <option>Resolvido</option>
      </select>
      <select x-model="filterCategoria"
        class="px-3 py-2 rounded-xl text-sm border outline-none bg-white"
        style="border-color:rgba(26,75,61,.15)">
        <option value="">Todas as categorias</option>
        <option>Alvará</option>
        <option>Declaração Anual</option>
        <option>DAS</option>
        <option>Cadastro</option>
        <option>Chatbot</option>
        <option>Geral</option>
      </select>
    </div>
  </div>

  <!-- Stats rápidas -->
  <div class="grid grid-cols-3 gap-3">
    <?php
    $pend = array_filter($protocols, fn($p)=>$p['status']==='Pendente');
    $anal = array_filter($protocols, fn($p)=>$p['status']==='Em Análise');
    $res  = array_filter($protocols, fn($p)=>$p['status']==='Resolvido');
    $qCards = [
      ['label'=>'Pendente',   'count'=>count($pend), 'bg'=>'#fef9c3','text'=>'#b45309'],
      ['label'=>'Em Análise', 'count'=>count($anal), 'bg'=>'#dbeafe','text'=>'#1d4ed8'],
      ['label'=>'Resolvido',  'count'=>count($res),  'bg'=>'#dcfce7','text'=>'#16a34a'],
    ];
    foreach ($qCards as $qc): ?>
    <div class="bg-white rounded-2xl p-4 border text-center" style="border-color:rgba(26,75,61,.1)">
      <p class="text-2xl font-bold text-[#031617]"><?= $qc['count'] ?></p>
      <span class="mt-1 inline-block px-2 py-0.5 rounded-full text-xs font-semibold"
        style="background:<?= $qc['bg'] ?>;color:<?= $qc['text'] ?>"><?= $qc['label'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Tabela -->
  <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(26,75,61,.08)">
      <h2 class="text-sm font-semibold text-[#031617]">Lista de Protocolos</h2>
      <span class="text-xs text-[#4a6b5a]" x-text="filtered.length + ' registro(s)'"></span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-xs">
        <thead class="bg-[#f8fcf8]">
          <tr>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Nº Protocolo</th>
            <?php if (session()->get('user_role') === 'servidor'): ?>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">MEI</th>
            <?php endif; ?>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Descrição</th>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Status</th>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Categoria</th>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Canal</th>
            <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Data</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y" style="divide-color:rgba(26,75,61,.06)">
          <template x-for="p in filtered" :key="p.id">
            <tr class="hover:bg-[#f8fcf8] transition-colors cursor-pointer"
              @click="openDetail(p)">
              <td class="px-5 py-3 font-semibold text-[#031617]" x-text="p.protocol_number"></td>
              <?php if (session()->get('user_role') === 'servidor'): ?>
              <td class="px-5 py-3 hidden md:table-cell">
                <p class="font-medium text-[#031617]" x-text="p.nome_fantasia || p.mei_nome || ''"></p>
              </td>
              <?php endif; ?>
              <td class="px-5 py-3 text-[#4a6b5a] max-w-[180px] hidden md:table-cell">
                <p class="truncate" x-text="p.descricao"></p>
              </td>
              <td class="px-5 py-3">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                  :style="statusStyle(p.status)" x-text="p.status"></span>
              </td>
              <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell" x-text="p.categoria"></td>
              <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell" x-text="p.canal"></td>
              <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell"
                x-text="new Date(p.created_at).toLocaleDateString('pt-BR')"></td>
              <td class="px-5 py-3">
                <i class="bi bi-chevron-right text-[#4a6b5a]"></i>
              </td>
            </tr>
          </template>
          <tr x-show="filtered.length === 0">
            <td colspan="9" class="text-center py-10 text-[#4a6b5a]">Nenhum protocolo encontrado</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal detalhe -->
  <div x-show="modal" x-cloak @click.self="modal=false"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" x-transition>
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="font-bold text-[#031617]" x-text="sel.protocol_number"></h3>
          <span class="mt-1 inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold"
            :style="statusStyle(sel.status)" x-text="sel.status"></span>
        </div>
        <button @click="modal=false"><i class="bi bi-x text-xl text-[#4a6b5a]"></i></button>
      </div>

      <div class="space-y-3 text-sm">
        <div class="grid grid-cols-2 gap-3 text-xs">
          <template x-if="sel.mei_nome">
            <div>
              <p class="text-[#4a6b5a]">MEI</p>
              <p class="font-semibold text-[#031617]" x-text="sel.nome_fantasia || sel.mei_nome"></p>
            </div>
          </template>
          <div>
            <p class="text-[#4a6b5a]">Categoria</p>
            <p class="font-semibold" x-text="sel.categoria"></p>
          </div>
          <div>
            <p class="text-[#4a6b5a]">Canal</p>
            <p x-text="sel.canal"></p>
          </div>
          <div>
            <p class="text-[#4a6b5a]">Data de abertura</p>
            <p x-text="sel.created_at ? new Date(sel.created_at).toLocaleDateString('pt-BR') : ''"></p>
          </div>
        </div>

        <div class="p-3 rounded-xl bg-[#f8fcf8] border text-xs" style="border-color:rgba(26,75,61,.1)">
          <p class="text-[#4a6b5a] mb-0.5">Descrição</p>
          <p class="text-[#031617]" x-text="sel.descricao"></p>
        </div>

        <template x-if="sel.observacao">
          <div class="p-3 rounded-xl" style="background:#f0f9f0;border:1px solid rgba(26,75,61,.12)">
            <p class="text-xs font-semibold text-[#1A4B3D] mb-1">
              <i class="bi bi-chat-square-text mr-1"></i>Resposta do servidor
            </p>
            <p class="text-xs text-[#031617]" x-text="sel.observacao"></p>
          </div>
        </template>
      </div>

      <div class="mt-5 pt-4 border-t flex gap-2" style="border-color:rgba(26,75,61,.08)">
        <button @click="modal=false"
          class="flex-1 py-2 rounded-xl text-xs border hover:bg-gray-50 transition-colors text-[#4a6b5a]"
          style="border-color:rgba(26,75,61,.15)">Fechar</button>
        <a href="<?= base_url('/chat') ?>"
          class="flex-1 py-2 rounded-xl text-xs font-semibold text-center"
          style="background:#1A4B3D;color:#fff">
          <i class="bi bi-chat-square-text mr-1"></i>Dúvidas? Fale com o Assistente
        </a>
      </div>
    </div>
  </div>
</div>

<script>
function protocolsPage() {
  return {
    search: '',
    filterStatus: '',
    filterCategoria: '',
    modal: false,
    sel: {},
    protocols: <?= json_encode($protocols, JSON_UNESCAPED_UNICODE) ?>,

    get filtered() {
      const q = this.search.toLowerCase();
      return this.protocols.filter(p => {
        const matchQ = !q || (p.protocol_number||'').toLowerCase().includes(q)
                           || (p.descricao||'').toLowerCase().includes(q)
                           || (p.mei_nome||'').toLowerCase().includes(q);
        const matchS = !this.filterStatus   || p.status    === this.filterStatus;
        const matchC = !this.filterCategoria|| p.categoria === this.filterCategoria;
        return matchQ && matchS && matchC;
      });
    },

    openDetail(p) { this.sel = p; this.modal = true; },

    statusStyle(s) {
      return {'Resolvido':'background:#dcfce7;color:#16a34a','Em Análise':'background:#dbeafe;color:#1d4ed8','Pendente':'background:#fef9c3;color:#b45309'}[s]
        || 'background:#f3f4f6;color:#374151';
    }
  }
}
</script>
