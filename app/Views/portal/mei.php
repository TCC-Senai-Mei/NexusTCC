<div class="space-y-6" x-data="portalMei()">

  <!-- Header info do MEI -->
  <div class="bg-white rounded-2xl p-5 border" style="border-color:rgba(26,75,61,.1)">
    <div class="flex flex-col md:flex-row md:items-center gap-4">
      <div class="flex items-center gap-4 flex-1">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 text-lg font-bold"
          style="background:#D6F2A6;color:#1A4B3D">
          <?php
            $n = session()->get('user_name');
            echo strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $n), 0, 2))));
          ?>
        </div>
        <div>
          <p class="font-bold text-[#031617]"><?= esc(session()->get('user_nome_fantasia') ?: session()->get('user_name')) ?></p>
          <p class="text-sm text-[#4a6b5a]"><?= esc(session()->get('user_name')) ?></p>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:#dcfce7;color:#16a34a">Regular</span>
            <span class="text-xs text-[#4a6b5a]">CNPJ: <?= esc(session()->get('user_cnpj')) ?></span>
          </div>
        </div>
      </div>
      <div class="flex flex-col gap-1 text-sm md:text-right">
        <p class="text-[#4a6b5a] text-xs">Atividade</p>
        <p class="font-semibold text-[#031617]"><?= esc(session()->get('user_atividade') ?: '—') ?></p>
        <p class="text-[#4a6b5a] text-xs"><?= esc(session()->get('user_municipio') ?: 'Nova Lima, MG') ?></p>
      </div>
      <button @click="novoModal=true"
        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold flex-shrink-0"
        style="background:#1A4B3D;color:#fff">
        <i class="bi bi-plus-circle"></i> Novo Protocolo
      </button>
    </div>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
  <div class="flex items-center gap-2 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
    <i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?>
  </div>
  <?php endif; ?>

  <!-- Grid: documentos + protocolos -->
  <div class="grid grid-cols-1 md:grid-cols-5 gap-5">

    <!-- Documentos -->
    <div class="md:col-span-2 bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
      <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(26,75,61,.08)">
        <h2 class="text-sm font-semibold text-[#031617]">Documentos</h2>
        <a href="https://www.gov.br/empresas-e-negocios/pt-br/empreendedor" target="_blank"
          class="text-xs text-[#1A4B3D] hover:underline">Portal MEI ↗</a>
      </div>
      <?php if (empty($documents)): ?>
      <p class="text-sm text-[#4a6b5a] text-center py-8">Nenhum documento cadastrado</p>
      <?php else: ?>
      <div class="divide-y" style="divide-color:rgba(26,75,61,.06)">
        <?php foreach ($documents as $doc):
          [$bg, $tx, $ic] = match(true) {
            $doc['status'] === 'Regular'           => ['#dcfce7', '#16a34a', 'check-circle-fill'],
            $doc['status'] === 'Atrasado'          => ['#fee2e2', '#dc2626', 'x-circle-fill'],
            str_contains($doc['status'],'Pendente')=> ['#fef9c3', '#b45309', 'clock-fill'],
            default                                => ['#e0f2fe', '#0369a1', 'info-circle-fill'],
          };
        ?>
        <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-[#f8fcf8] transition-colors">
          <i class="bi bi-<?= $ic ?> flex-shrink-0" style="color:<?= $tx ?>"></i>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-[#031617] truncate"><?= esc($doc['tipo']) ?></p>
            <?php if ($doc['vencimento']): ?>
            <p class="text-[10px] text-[#4a6b5a]">Vence: <?= date('d/m/Y', strtotime($doc['vencimento'])) ?></p>
            <?php endif; ?>
          </div>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap"
            style="background:<?= $bg ?>;color:<?= $tx ?>"><?= esc($doc['status']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Protocolos -->
    <div class="md:col-span-3 bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
      <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(26,75,61,.08)">
        <h2 class="text-sm font-semibold text-[#031617]">Meus Protocolos</h2>
        <a href="<?= base_url('/protocolos') ?>" class="text-xs text-[#1A4B3D] hover:underline">Ver todos</a>
      </div>

      <?php if (empty($protocols)): ?>
      <div class="text-center py-12">
        <i class="bi bi-file-earmark-text text-4xl" style="color:#D6F2A6"></i>
        <p class="text-sm text-[#4a6b5a] mt-3">Nenhum protocolo aberto ainda.</p>
        <button @click="novoModal=true"
          class="mt-4 px-5 py-2 rounded-xl text-sm font-semibold"
          style="background:#1A4B3D;color:#fff">
          <i class="bi bi-plus"></i> Abrir meu primeiro protocolo
        </button>
      </div>
      <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead class="bg-[#f8fcf8]">
            <tr>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Protocolo</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Descrição</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold">Status</th>
              <th class="text-left px-5 py-3 text-[#4a6b5a] font-semibold hidden md:table-cell">Data</th>
              <th class="px-5 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y" style="divide-color:rgba(26,75,61,.06)">
            <?php foreach ($protocols as $p):
              $sc = match($p['status']) {
                'Resolvido'  => ['bg'=>'#dcfce7','text'=>'#16a34a'],
                'Em Análise' => ['bg'=>'#dbeafe','text'=>'#1d4ed8'],
                default      => ['bg'=>'#fef9c3','text'=>'#b45309'],
              };
            ?>
            <tr class="hover:bg-[#f8fcf8] transition-colors cursor-pointer"
              @click="openProtocol(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)">
              <td class="px-5 py-3 font-semibold text-[#031617]"><?= esc($p['protocol_number']) ?></td>
              <td class="px-5 py-3 text-[#4a6b5a] max-w-[180px] truncate hidden md:table-cell"><?= esc($p['descricao']) ?></td>
              <td class="px-5 py-3">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                  style="background:<?= $sc['bg'] ?>;color:<?= $sc['text'] ?>">
                  <?= $p['status'] ?>
                </span>
              </td>
              <td class="px-5 py-3 text-[#4a6b5a] hidden md:table-cell"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
              <td class="px-5 py-3">
                <i class="bi bi-chevron-right text-[#4a6b5a]"></i>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal: Novo Protocolo -->
  <div x-show="novoModal" x-cloak @click.self="novoModal=false"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" x-transition>
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[#031617]">Abrir Novo Protocolo</h3>
        <button @click="novoModal=false"><i class="bi bi-x text-xl text-[#4a6b5a]"></i></button>
      </div>
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Categoria</label>
          <select x-model="novoForm.categoria"
            class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
            style="border-color:rgba(26,75,61,.2)">
            <option value="">Selecionar...</option>
            <option>Alvará</option>
            <option>Declaração Anual</option>
            <option>DAS</option>
            <option>Cadastro</option>
            <option>Geral</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Descrição da solicitação</label>
          <textarea x-model="novoForm.descricao" rows="4"
            placeholder="Descreva sua necessidade de forma clara e objetiva..."
            class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D] resize-none"
            style="border-color:rgba(26,75,61,.2)"></textarea>
        </div>
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Observações adicionais</label>
          <input type="text" x-model="novoForm.observacao"
            placeholder="(opcional)"
            class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
            style="border-color:rgba(26,75,61,.2)">
        </div>

        <!-- Sucesso -->
        <div x-show="novoSucesso" x-cloak
          class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl"
          style="background:#D6F2A6;border:1px solid rgba(26,75,61,.2)">
          <i class="bi bi-check-circle-fill text-[#1A4B3D]"></i>
          <div>
            <p class="text-xs font-semibold text-[#1A4B3D]">Protocolo aberto com sucesso!</p>
            <p class="text-[10px] text-[#4a6b5a]" x-text="'Número: ' + novoNumero"></p>
          </div>
        </div>

        <button @click="criarProtocolo()"
          :disabled="novoLoading || !novoForm.descricao || !novoForm.categoria"
          class="w-full py-2.5 rounded-xl text-sm font-semibold disabled:opacity-50 transition-opacity"
          style="background:#1A4B3D;color:#fff">
          <span x-show="!novoLoading"><i class="bi bi-send mr-1"></i> Enviar Protocolo</span>
          <span x-show="novoLoading">Enviando...</span>
        </button>
      </div>
    </div>
  </div>

  <!-- Modal: Detalhe Protocolo -->
  <div x-show="detalheModal" x-cloak @click.self="detalheModal=false"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" x-transition>
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[#031617]" x-text="sel.protocol_number"></h3>
        <button @click="detalheModal=false"><i class="bi bi-x text-xl text-[#4a6b5a]"></i></button>
      </div>
      <div class="space-y-3 text-sm">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <p class="text-xs text-[#4a6b5a]">Status</p>
            <p class="font-semibold text-[#031617]" x-text="sel.status"></p>
          </div>
          <div>
            <p class="text-xs text-[#4a6b5a]">Canal</p>
            <p class="font-semibold" x-text="sel.canal"></p>
          </div>
          <div>
            <p class="text-xs text-[#4a6b5a]">Categoria</p>
            <p x-text="sel.categoria"></p>
          </div>
          <div>
            <p class="text-xs text-[#4a6b5a]">Data</p>
            <p x-text="sel.created_at ? new Date(sel.created_at).toLocaleDateString('pt-BR') : ''"></p>
          </div>
        </div>
        <div class="pt-2 border-t" style="border-color:rgba(26,75,61,.1)">
          <p class="text-xs text-[#4a6b5a] mb-1">Descrição</p>
          <p class="text-[#031617]" x-text="sel.descricao"></p>
        </div>
        <template x-if="sel.observacao">
          <div class="p-3 rounded-xl" style="background:#f0f9f0;border:1px solid rgba(26,75,61,.12)">
            <p class="text-xs font-semibold text-[#1A4B3D] mb-1">Resposta do servidor</p>
            <p class="text-xs text-[#031617]" x-text="sel.observacao"></p>
          </div>
        </template>
      </div>
    </div>
  </div>
</div>

<script>
function portalMei() {
  return {
    novoModal: false, novoSucesso: false, novoLoading: false, novoNumero: '',
    detalheModal: false, sel: {},
    novoForm: { categoria: '', descricao: '', observacao: '' },

    openProtocol(p) { this.sel = p; this.detalheModal = true; },

    async criarProtocolo() {
      if (!this.novoForm.descricao || !this.novoForm.categoria) return;
      this.novoLoading = true;
      try {
        const res = await fetch('<?= base_url('/api/protocolo') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify(this.novoForm)
        });
        const data = await res.json();
        if (data.ok) {
          this.novoSucesso = true;
          this.novoNumero = data.numero;
          this.novoForm = { categoria: '', descricao: '', observacao: '' };
          setTimeout(() => {
            this.novoModal = false;
            this.novoSucesso = false;
            window.location.reload();
          }, 2500);
        }
      } catch (e) { alert('Erro ao criar protocolo. Tente novamente.'); }
      this.novoLoading = false;
    }
  }
}
</script>
