<div class="max-w-2xl space-y-6" x-data="settingsPage()">

  <div>
    <h1 class="text-xl font-bold text-[#031617]">Configurações</h1>
    <p class="text-sm text-[#4a6b5a] mt-0.5">Gerencie seus dados de perfil e preferências</p>
  </div>

  <?php if (!empty($success)): ?>
  <div class="flex items-center gap-2 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
    <i class="bi bi-check-circle-fill"></i> <?= esc($success) ?>
  </div>
  <?php endif; ?>

  <!-- Tabs -->
  <div class="flex gap-1 bg-[#f8fcf8] p-1 rounded-xl w-fit border" style="border-color:rgba(26,75,61,.1)">
    <button @click="tab='perfil'"
      :class="tab==='perfil' ? 'bg-white shadow-sm text-[#1A4B3D] font-semibold' : 'text-[#4a6b5a]'"
      class="px-4 py-2 rounded-lg text-sm transition-all">
      <i class="bi bi-person mr-1.5"></i>Perfil
    </button>
    <button @click="tab='seguranca'"
      :class="tab==='seguranca' ? 'bg-white shadow-sm text-[#1A4B3D] font-semibold' : 'text-[#4a6b5a]'"
      class="px-4 py-2 rounded-lg text-sm transition-all">
      <i class="bi bi-shield mr-1.5"></i>Segurança
    </button>
    <button @click="tab='notificacoes'"
      :class="tab==='notificacoes' ? 'bg-white shadow-sm text-[#1A4B3D] font-semibold' : 'text-[#4a6b5a]'"
      class="px-4 py-2 rounded-lg text-sm transition-all">
      <i class="bi bi-bell mr-1.5"></i>Notificações
    </button>
  </div>

  <!-- ─── Tab Perfil ────────────────────────────────────── -->
  <div x-show="tab==='perfil'" x-transition.opacity>
    <form action="<?= base_url('/configuracoes') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
        <!-- Avatar -->
        <div class="flex items-center gap-4 px-6 py-5 border-b" style="border-color:rgba(26,75,61,.08)">
          <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-bold flex-shrink-0"
            style="background:#D6F2A6;color:#1A4B3D">
            <?php
              $n = session()->get('user_name');
              echo strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $n), 0, 2))));
            ?>
          </div>
          <div>
            <p class="font-bold text-[#031617]"><?= esc(session()->get('user_name')) ?></p>
            <p class="text-sm text-[#4a6b5a]">
              <?= session()->get('user_role') === 'mei'
                ? 'MEI · ' . esc(session()->get('user_cnpj'))
                : 'Servidor · Mat. ' . esc(session()->get('user_matricula')) ?>
            </p>
          </div>
        </div>

        <!-- Campos -->
        <div class="p-6 space-y-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Nome completo</label>
              <input type="text" name="nome" value="<?= esc(session()->get('user_name')) ?>"
                class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D] transition-colors"
                style="border-color:rgba(26,75,61,.2)">
            </div>
            <div>
              <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">E-mail</label>
              <input type="email" name="email" value="<?= esc(session()->get('user_email')) ?>"
                class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
                style="border-color:rgba(26,75,61,.2)">
            </div>
            <div>
              <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Telefone</label>
              <input type="tel" name="telefone" value="<?= esc($user['telefone'] ?? '') ?>"
                x-model="tel" @input="tel=maskTel(tel)"
                placeholder="(31) 99999-9999" maxlength="15"
                class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
                style="border-color:rgba(26,75,61,.2)">
            </div>

            <?php if (session()->get('user_role') === 'mei'): ?>
            <div>
              <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">CNPJ</label>
              <input type="text" value="<?= esc(session()->get('user_cnpj')) ?>" readonly
                class="w-full px-3 py-2.5 rounded-xl text-sm border bg-[#f8fcf8] text-[#4a6b5a] cursor-not-allowed"
                style="border-color:rgba(26,75,61,.1)">
            </div>
            <div>
              <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Nome Fantasia</label>
              <input type="text" value="<?= esc(session()->get('user_nome_fantasia')) ?>" readonly
                class="w-full px-3 py-2.5 rounded-xl text-sm border bg-[#f8fcf8] text-[#4a6b5a] cursor-not-allowed"
                style="border-color:rgba(26,75,61,.1)">
            </div>
            <?php else: ?>
            <div>
              <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Matrícula</label>
              <input type="text" value="<?= esc(session()->get('user_matricula')) ?>" readonly
                class="w-full px-3 py-2.5 rounded-xl text-sm border bg-[#f8fcf8] text-[#4a6b5a] cursor-not-allowed"
                style="border-color:rgba(26,75,61,.1)">
            </div>
            <?php endif; ?>
          </div>

          <div class="flex justify-end pt-2">
            <button type="submit"
              class="px-6 py-2.5 rounded-xl text-sm font-semibold"
              style="background:#1A4B3D;color:#fff">
              <i class="bi bi-check2 mr-1"></i> Salvar alterações
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- ─── Tab Segurança ─────────────────────────────────── -->
  <div x-show="tab==='seguranca'" x-transition.opacity>
    <form action="<?= base_url('/configuracoes') ?>" method="POST">
      <?= csrf_field() ?>
      <!-- Campos obrigatórios do perfil (hidden) -->
      <input type="hidden" name="nome"  value="<?= esc(session()->get('user_name')) ?>">
      <input type="hidden" name="email" value="<?= esc(session()->get('user_email')) ?>">
      <input type="hidden" name="telefone" value="">

      <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
        <div class="px-6 py-5 border-b" style="border-color:rgba(26,75,61,.08)">
          <h2 class="text-sm font-semibold text-[#031617]">Alterar Senha</h2>
          <p class="text-xs text-[#4a6b5a] mt-0.5">Mínimo de 6 caracteres</p>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Senha atual</label>
            <input type="password" name="senha_atual" placeholder="••••••••"
              class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
              style="border-color:rgba(26,75,61,.2)">
          </div>
          <div>
            <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Nova senha</label>
            <input type="password" name="nova_senha" placeholder="••••••••" minlength="6"
              class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
              style="border-color:rgba(26,75,61,.2)">
          </div>
          <div>
            <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Confirmar nova senha</label>
            <input type="password" placeholder="••••••••"
              class="w-full px-3 py-2.5 rounded-xl text-sm border outline-none focus:border-[#1A4B3D]"
              style="border-color:rgba(26,75,61,.2)">
          </div>
          <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-700">
            <i class="bi bi-exclamation-triangle mr-1"></i>
            Após alterar a senha você será redirecionado para o login.
          </div>
          <div class="flex justify-end pt-1">
            <button type="submit"
              class="px-6 py-2.5 rounded-xl text-sm font-semibold"
              style="background:#1A4B3D;color:#fff">
              <i class="bi bi-shield-check mr-1"></i> Alterar Senha
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- ─── Tab Notificações ─────────────────────────────── -->
  <div x-show="tab==='notificacoes'" x-transition.opacity>
    <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(26,75,61,.1)">
      <div class="px-6 py-5 border-b" style="border-color:rgba(26,75,61,.08)">
        <h2 class="text-sm font-semibold text-[#031617]">Preferências de Notificação</h2>
      </div>
      <div class="p-6 space-y-4">
        <?php $toggles = [
          ['label'=>'Vencimento de documentos',   'desc'=>'Alerta quando um documento estiver próximo do vencimento', 'default'=>true],
          ['label'=>'Atualização de protocolos',  'desc'=>'Notificação quando o status de um protocolo mudar',       'default'=>true],
          ['label'=>'Novos comunicados',           'desc'=>'Avisos e comunicados da Sala do Empreendedor',             'default'=>false],
          ['label'=>'Resumo semanal',              'desc'=>'E-mail com resumo dos seus documentos e protocolos',       'default'=>false],
        ];
        foreach ($toggles as $t): ?>
        <div class="flex items-center justify-between py-3 border-b last:border-0" style="border-color:rgba(26,75,61,.06)">
          <div>
            <p class="text-sm font-semibold text-[#031617]"><?= $t['label'] ?></p>
            <p class="text-xs text-[#4a6b5a] mt-0.5"><?= $t['desc'] ?></p>
          </div>
          <button type="button" x-data="{on: <?= $t['default'] ? 'true' : 'false' ?>}"
            @click="on=!on"
            :class="on ? 'bg-[#1A4B3D]' : 'bg-gray-200'"
            class="relative w-10 h-5 rounded-full transition-colors flex-shrink-0">
            <span class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"
              :class="on ? 'translate-x-5' : 'translate-x-0'"></span>
          </button>
        </div>
        <?php endforeach; ?>
        <div class="flex justify-end pt-2">
          <button type="button"
            class="px-6 py-2.5 rounded-xl text-sm font-semibold"
            style="background:#1A4B3D;color:#fff">
            <i class="bi bi-check2 mr-1"></i> Salvar preferências
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function settingsPage() {
  return {
    tab: 'perfil',
    tel: '<?= esc($user['telefone'] ?? '') ?>',
    maskTel(v) {
      v = v.replace(/\D/g,'').slice(0,11);
      return v.replace(/^(\d{2})(\d)/,'($1) $2').replace(/(\d{5})(\d)/,'$1-$2');
    }
  }
}
</script>
