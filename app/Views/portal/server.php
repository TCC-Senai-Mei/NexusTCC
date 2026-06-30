<div class="space-y-6" x-data="serverPanel()">

  <!-- Header + tabs -->
  <div>
    <h1 class="text-xl font-bold text-[#031617]">Painel de Atendimento</h1>
    <p class="text-sm text-[#4a6b5a] mt-0.5">Gerenciamento de MEIs e protocolos — Sala do Empreendedor</p>
  </div>

  <!-- Tab bar -->
  <div class="flex gap-1 bg-[#f8fcf8] p-1 rounded-xl w-fit border" style="border-color:rgba(26,75,61,.1)">
    <button @click="tab='protocolos'"
      :class="tab==='protocolos' ? 'bg-white shadow-sm text-[#1A4B3D] font-semibold' : 'text-[#4a6b5a]'"
      class="px-4 py-2 rounded-lg text-sm transition-all">
      <i class="bi bi-file-earmark-text mr-1.5"></i>Protocolos
    </button>
    <button @click="tab='meis'"
      :class="tab==='meis' ? 'bg-white shadow-sm text-[#1A4B3D] font-semibold' : 'text-[#4a6b5a]'"
      class="px-4 py-2 rounded-lg text-sm transition-all">
      <i class="bi bi-people mr-1.5"></i>MEIs Cadastrados
    </button>
  </div>

  <!-- ─── Tab: Protocolos ────────────────────────────────────── -->
  <div x-show="tab==='protocolos'" x-transition.opacity>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl p-4 border mb-4" style="border-color:rgba(26,75,61,.1)">
      <div class="flex flex-col md:flex-row gap-3">
        <input type="text" x-model="searchProtocol" placeholder="Buscar por número, MEI ou descrição..."
          class="flex-1 px-3 py-2 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
          style="border-color:rgba(26,75,61,.15)">
        <select x-model="filterStatus"
          class="px-3 py-2 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
          style="border-color:rgba(26,75,61,.15)">
          <option value="">Todos os status</option>
          <option>Pendente</option>
          <option>Em Análise</option>
          <option>Resolvido</option>
        </select>
        <select x-model="filterCanal"
          class="px-3 py-2 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
          style="border-color:rgba(26,75,61,.15)">
          <option value="">Todos os canais</option>
          <option>Portal</option>
          <option>Chatbot</option>
          <option>Presencial</option>
        </select>
      </div>
    </div>

    <!-- Tabela de protocolos -->
    <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
      <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(26,75,61,.08)">
        <h2 class="text-sm font-semibold text-[#031617]">Todos os Protocolos</h2>
        <span class="text-xs text-[#4a6b5a]"
          x-text="filteredProtocols.length + ' resultado(s)'"></span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead class="bg-[#f8fcf8]">
            <tr>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Nº Protocolo</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">MEI</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Descrição</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Status</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Canal</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Data</th>
              <th class="px-5 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y" style="divide-color:rgba(26,75,61,.06)">
            <template x-for="p in filteredProtocols" :key="p.id">
              <tr class="hover:bg-[#f8fcf8] transition-colors">
                <td class="px-5 py-3 font-semibold text-[#031617]" x-text="p.protocol_number"></td>
                <td class="px-5 py-3">
                  <p class="font-medium text-[#031617] truncate max-w-[120px]" x-text="p.nome_fantasia || p.mei_nome"></p>
                  <p class="text-[10px] text-[#4a6b5a] truncate max-w-[120px]" x-text="p.cnpj"></p>
                </td>
                <td class="px-5 py-3 text-[#4a6b5a] max-w-[160px] hidden md:table-cell">
                  <p class="truncate" x-text="p.descricao"></p>
                </td>
                <td class="px-5 py-3">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                    :style="statusStyle(p.status)"
                    x-text="p.status"></span>
                </td>
                <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell" x-text="p.canal"></td>
                <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell"
                  x-text="new Date(p.created_at).toLocaleDateString('pt-BR')"></td>
                <td class="px-5 py-3">
                  <button @click="openEdit(p)"
                    class="text-xs text-[#1A4B3D] font-medium hover:underline">Gerenciar</button>
                </td>
              </tr>
            </template>
            <tr x-show="filteredProtocols.length === 0">
              <td colspan="7" class="text-center py-8 text-[#4a6b5a]">Nenhum protocolo encontrado</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ─── Tab: MEIs ────────────────────────────────────────── -->
  <div x-show="tab==='meis'" x-transition.opacity>
    <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
      <div class="px-5 py-4 border-b" style="border-color:rgba(26,75,61,.08)">
        <h2 class="text-sm font-semibold text-[#031617]">MEIs Cadastrados — Nova Lima</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead class="bg-[#f8fcf8]">
            <tr>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Nome / Empresa</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">CNPJ</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Atividade</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Contato</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Situação</th>
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
              <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell"><?= esc($m['atividade']) ?></td>
              <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell"><?= esc($m['telefone']) ?></td>
              <td class="px-5 py-3">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                  style="background:#dcfce7;color:#16a34a">Regular</span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal: Gerenciar Protocolo -->
  <div x-show="editModal" x-cloak @click.self="editModal=false"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" x-transition>
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[#031617]" x-text="editSel.protocol_number"></h3>
        <button @click="editModal=false"><i class="bi bi-x text-xl text-[#4a6b5a]"></i></button>
      </div>

      <div class="space-y-3 text-sm mb-4">
        <div class="grid grid-cols-2 gap-3 text-xs">
          <div>
            <p class="text-[#4a6b5a]">MEI</p>
            <p class="font-semibold text-[#031617]" x-text="editSel.mei_nome"></p>
          </div>
          <div>
            <p class="text-[#4a6b5a]">CNPJ</p>
            <p x-text="editSel.cnpj"></p>
          </div>
          <div>
            <p class="text-[#4a6b5a]">Categoria</p>
            <p x-text="editSel.categoria"></p>
          </div>
          <div>
            <p class="text-[#4a6b5a]">Canal</p>
            <p x-text="editSel.canal"></p>
          </div>
        </div>
        <div class="p-3 rounded-xl bg-[#f8fcf8] border text-xs" style="border-color:rgba(26,75,61,.1)">
          <p class="text-[#4a6b5a] mb-0.5">Descrição</p>
          <p class="text-[#031617]" x-text="editSel.descricao"></p>
        </div>
        <!-- Status atual -->
        <div>
          <p class="text-xs text-[#4a6b5a] mb-1">Status atual</p>
          <span class="px-2 py-1 rounded-full text-xs font-semibold"
            :style="statusStyle(editSel.status)"
            x-text="editSel.status"></span>
        </div>
      </div>

      <!-- Para este TCC: o gerenciamento de protocolo seria feito aqui -->
      <div class="border-t pt-4 space-y-3" style="border-color:rgba(26,75,61,.08)">
        <p class="text-xs font-semibold text-[#4a6b5a]">Ações rápidas</p>
        <div class="flex gap-2">
          <button class="flex-1 py-2 rounded-xl text-xs font-semibold border hover:bg-[#f0f9f0] transition-colors"
            style="border-color:rgba(26,75,61,.2);color:#1A4B3D">
            <i class="bi bi-chat-text mr-1"></i>Contactar MEI
          </button>
          <a href="<?= base_url('/protocolos') ?>"
            class="flex-1 py-2 rounded-xl text-xs font-semibold text-center"
            style="background:#1A4B3D;color:#fff">
            <i class="bi bi-arrow-right mr-1"></i>Ver em Protocolos
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function serverPanel() {
  return {
    tab: 'protocolos',
    searchProtocol: '',
    filterStatus: '',
    filterCanal: '',
    editModal: false,
    editSel: {},
    protocols: <?= json_encode($protocols, JSON_UNESCAPED_UNICODE) ?>,

    get filteredProtocols() {
      return this.protocols.filter(p => {
        const q = this.searchProtocol.toLowerCase();
        const matchSearch = !q ||
          p.protocol_number.toLowerCase().includes(q) ||
          (p.mei_nome || '').toLowerCase().includes(q) ||
          (p.nome_fantasia || '').toLowerCase().includes(q) ||
          (p.descricao || '').toLowerCase().includes(q);
        const matchStatus = !this.filterStatus || p.status === this.filterStatus;
        const matchCanal  = !this.filterCanal  || p.canal  === this.filterCanal;
        return matchSearch && matchStatus && matchCanal;
      });
    },

    openEdit(p) { this.editSel = p; this.editModal = true; },

    statusStyle(status) {
      const map = {
        'Resolvido'  : 'background:#dcfce7;color:#16a34a',
        'Em Análise' : 'background:#dbeafe;color:#1d4ed8',
        'Pendente'   : 'background:#fef9c3;color:#b45309',
      };
      return map[status] || 'background:#f3f4f6;color:#374151';
    }
  }
}
</script>
