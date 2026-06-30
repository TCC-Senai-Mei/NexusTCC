<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal Nexus — Login</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  [x-cloak]{display:none!important}
  input:focus{outline:none;border-color:#1A4B3D !important}
  .btn-primary{background:#1A4B3D;color:#fff;transition:opacity .15s}
  .btn-primary:hover{opacity:.88}
  .card{background:#fff;border:1px solid rgba(26,75,61,.12);border-radius:1.25rem;box-shadow:0 2px 12px rgba(26,75,61,.07)}
</style>
</head>

<body class="min-h-screen bg-[#f8fcf8] flex items-center justify-center p-4"
  x-data="loginApp()" x-cloak>

<div class="w-full max-w-md">

  <!-- Logo -->
  <div class="flex items-center justify-center gap-2.5 mb-8">
    <svg width="32" height="32" viewBox="0 0 36 36" fill="none">
      <rect x="2" y="12" width="14" height="14" rx="2" fill="#D6F2A6"/>
      <rect x="10" y="4" width="14" height="14" rx="2" fill="#1A4B3D"/>
      <rect x="20" y="18" width="14" height="14" rx="2" fill="#2d7a5f"/>
    </svg>
    <div>
      <p class="font-bold text-[#1A4B3D] text-2xl leading-tight" style="font-family:system-ui">nexus</p>
      <p class="text-xs text-[#4a6b5a] leading-tight">Sala do Empreendedor — Nova Lima</p>
    </div>
  </div>

  <!-- Flash messages -->
  <?php if (!empty($error)): ?>
  <div class="mb-4 flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
    <i class="bi bi-exclamation-circle flex-shrink-0"></i> <?= esc($error) ?>
  </div>
  <?php endif; ?>
  <?php if (!empty($signup_error)): ?>
  <div class="mb-4 flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
    <i class="bi bi-exclamation-circle flex-shrink-0"></i> <?= esc($signup_error) ?>
  </div>
  <?php endif; ?>
  <?php if (!empty($signup_success)): ?>
  <div class="mb-4 flex items-center gap-2 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
    <i class="bi bi-check-circle flex-shrink-0"></i> <?= esc($signup_success) ?>
  </div>
  <?php endif; ?>

  <!-- ─── PORTAL CHOICE ─────────────────────────────────────────── -->
  <div x-show="screen === 'choice'" x-transition.opacity class="card p-7">
    <h1 class="text-xl font-bold text-[#031617] mb-1">Bem-vindo ao Portal Nexus</h1>
    <p class="text-sm text-[#4a6b5a] mb-6">Escolha como deseja acessar.</p>

    <div class="space-y-3">
      <button @click="screen='mei'"
        class="w-full flex items-center gap-4 p-4 rounded-2xl border text-left hover:border-[#1A4B3D] hover:bg-[#f0f9f0] transition-all group"
        style="border-color:rgba(26,75,61,.18)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
          style="background:#D6F2A6">
          <i class="bi bi-building text-[#1A4B3D] text-lg"></i>
        </div>
        <div class="flex-1">
          <p class="font-semibold text-[#031617] text-sm">Área do MEI</p>
          <p class="text-xs text-[#4a6b5a]">Microempreendedor Individual</p>
        </div>
        <i class="bi bi-chevron-right text-[#4a6b5a] group-hover:text-[#1A4B3D]"></i>
      </button>

      <button @click="screen='servidor'"
        class="w-full flex items-center gap-4 p-4 rounded-2xl border text-left hover:border-[#1A4B3D] hover:bg-[#f0f9f0] transition-all group"
        style="border-color:rgba(26,75,61,.18)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
          style="background:#e8f5e9">
          <i class="bi bi-shield-check text-[#1A4B3D] text-lg"></i>
        </div>
        <div class="flex-1">
          <p class="font-semibold text-[#031617] text-sm">Área do Servidor</p>
          <p class="text-xs text-[#4a6b5a]">Prefeitura de Nova Lima</p>
        </div>
        <i class="bi bi-chevron-right text-[#4a6b5a] group-hover:text-[#1A4B3D]"></i>
      </button>
    </div>
  </div>

  <!-- ─── MEI LOGIN ──────────────────────────────────────────────── -->
  <div x-show="screen === 'mei'" x-transition.opacity class="card p-7">
    <button @click="screen='choice'" class="flex items-center gap-1.5 text-xs text-[#4a6b5a] mb-5 hover:text-[#1A4B3D]">
      <i class="bi bi-arrow-left"></i> Voltar
    </button>
    <h2 class="text-lg font-bold text-[#031617] mb-1">Acesso MEI</h2>
    <p class="text-sm text-[#4a6b5a] mb-5">Entre com seu CNPJ e senha.</p>

    <form action="<?= base_url('/auth/mei') ?>" method="POST" class="space-y-4">
      <?= csrf_field() ?>
      <div>
        <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">CNPJ</label>
        <input type="text" name="cnpj" x-model="cnpj" @input="cnpj=maskCnpj(cnpj)"
          placeholder="00.000.000/0001-00" maxlength="18"
          class="w-full px-3 py-2.5 rounded-xl text-sm border bg-[#f8fcf8] transition-colors"
          style="border-color:rgba(26,75,61,.2)" required>
      </div>
      <div>
        <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Senha</label>
        <div class="relative">
          <input :type="showPw ? 'text' : 'password'" name="senha"
            placeholder="••••••••"
            class="w-full px-3 py-2.5 pr-10 rounded-xl text-sm border bg-[#f8fcf8] transition-colors"
            style="border-color:rgba(26,75,61,.2)" required>
          <button type="button" @click="showPw=!showPw"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#4a6b5a] hover:text-[#1A4B3D]">
            <i :class="showPw ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-primary w-full py-2.5 rounded-xl font-semibold text-sm mt-1">
        Entrar <i class="bi bi-arrow-right ml-1"></i>
      </button>
    </form>

    <div class="mt-4 text-center space-y-1.5">
      <button @click="screen='signup'" class="text-xs text-[#1A4B3D] hover:underline block w-full">
        Não tenho cadastro — criar conta
      </button>
      <button @click="screen='recover'" class="text-xs text-[#4a6b5a] hover:underline block w-full">
        Esqueci minha senha
      </button>
    </div>

    <div class="mt-5 p-3 rounded-xl bg-[#f0f9f0] border text-xs text-[#4a6b5a]" style="border-color:rgba(26,75,61,.12)">
      <strong class="text-[#1A4B3D]">Demo:</strong> CNPJ <code>12.345.678/0001-90</code> / senha <code>senha123</code>
    </div>
  </div>

  <!-- ─── MEI CADASTRO ───────────────────────────────────────────── -->
  <div x-show="screen === 'signup'" x-transition.opacity class="card p-7">
    <button @click="screen='mei'" class="flex items-center gap-1.5 text-xs text-[#4a6b5a] mb-5 hover:text-[#1A4B3D]">
      <i class="bi bi-arrow-left"></i> Voltar
    </button>
    <h2 class="text-lg font-bold text-[#031617] mb-1">Criar conta MEI</h2>
    <p class="text-sm text-[#4a6b5a] mb-5">Preencha seus dados para se cadastrar.</p>

    <form action="<?= base_url('/auth/cadastro') ?>" method="POST" class="space-y-3">
      <?= csrf_field() ?>
      <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2">
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1">Nome completo</label>
          <input type="text" name="nome" placeholder="João da Silva"
            class="w-full px-3 py-2 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)" required>
        </div>
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1">Nome Fantasia</label>
          <input type="text" name="nome_fantasia" placeholder="Minha Empresa"
            class="w-full px-3 py-2 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)">
        </div>
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1">CNPJ</label>
          <input type="text" name="cnpj" x-model="sigCnpj" @input="sigCnpj=maskCnpj(sigCnpj)"
            placeholder="00.000.000/0001-00" maxlength="18"
            class="w-full px-3 py-2 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)" required>
        </div>
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1">Telefone</label>
          <input type="tel" name="telefone" x-model="sigTel" @input="sigTel=maskTel(sigTel)"
            placeholder="(31) 99999-9999" maxlength="15"
            class="w-full px-3 py-2 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)">
        </div>
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1">E-mail</label>
          <input type="email" name="email" placeholder="email@empresa.com"
            class="w-full px-3 py-2 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)" required>
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1">Atividade principal</label>
          <select name="atividade"
            class="w-full px-3 py-2 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)">
            <option value="">Selecionar...</option>
            <option>Instalações Elétricas</option>
            <option>Fabricação de Doces e Salgados</option>
            <option>Jardinagem e Paisagismo</option>
            <option>Cabeleireiro(a)</option>
            <option>Manicure / Pedicure</option>
            <option>Costureira(o)</option>
            <option>Motorista de Aplicativo</option>
            <option>Pedreiro / Construção Civil</option>
            <option>Mecânico(a)</option>
            <option>Designer Gráfico</option>
            <option>Fotógrafo(a)</option>
            <option>Vendedor(a) Ambulante</option>
            <option>Encanador</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1">Senha</label>
          <input type="password" name="senha" placeholder="Mín. 6 caracteres"
            class="w-full px-3 py-2 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)" required minlength="6">
        </div>
        <div>
          <label class="block text-xs font-medium text-[#4a6b5a] mb-1">Confirmar senha</label>
          <input type="password" name="senha_conf" placeholder="Repita a senha"
            class="w-full px-3 py-2 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)" required minlength="6">
        </div>
      </div>
      <button type="submit" class="btn-primary w-full py-2.5 rounded-xl font-semibold text-sm mt-1">
        Criar conta <i class="bi bi-person-plus ml-1"></i>
      </button>
    </form>
  </div>

  <!-- ─── SERVIDOR LOGIN ─────────────────────────────────────────── -->
  <div x-show="screen === 'servidor'" x-transition.opacity class="card p-7">
    <button @click="screen='choice'" class="flex items-center gap-1.5 text-xs text-[#4a6b5a] mb-5 hover:text-[#1A4B3D]">
      <i class="bi bi-arrow-left"></i> Voltar
    </button>
    <h2 class="text-lg font-bold text-[#031617] mb-1">Acesso do Servidor</h2>
    <p class="text-sm text-[#4a6b5a] mb-5">Entre com sua matrícula funcional.</p>

    <form action="<?= base_url('/auth/servidor') ?>" method="POST" class="space-y-4">
      <?= csrf_field() ?>
      <div>
        <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Matrícula</label>
        <input type="text" name="matricula" placeholder="0000000"
          class="w-full px-3 py-2.5 rounded-xl text-sm border bg-[#f8fcf8] transition-colors"
          style="border-color:rgba(26,75,61,.2)" required>
      </div>
      <div>
        <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">Senha</label>
        <div class="relative">
          <input :type="showServPw ? 'text' : 'password'" name="senha"
            placeholder="••••••••"
            class="w-full px-3 py-2.5 pr-10 rounded-xl text-sm border bg-[#f8fcf8]"
            style="border-color:rgba(26,75,61,.2)" required>
          <button type="button" @click="showServPw=!showServPw"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#4a6b5a] hover:text-[#1A4B3D]">
            <i :class="showServPw ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-primary w-full py-2.5 rounded-xl font-semibold text-sm">
        Entrar <i class="bi bi-arrow-right ml-1"></i>
      </button>
    </form>

    <div class="mt-4 p-3 rounded-xl bg-[#f0f9f0] border text-xs text-[#4a6b5a]" style="border-color:rgba(26,75,61,.12)">
      <strong class="text-[#1A4B3D]">Demo:</strong> Matrícula <code>2025001</code> / senha <code>prefeitura2025</code>
    </div>
  </div>

  <!-- ─── RECOVER ────────────────────────────────────────────────── -->
  <div x-show="screen === 'recover'" x-transition.opacity class="card p-7">
    <button @click="screen='mei'" class="flex items-center gap-1.5 text-xs text-[#4a6b5a] mb-5 hover:text-[#1A4B3D]">
      <i class="bi bi-arrow-left"></i> Voltar
    </button>
    <h2 class="text-lg font-bold text-[#031617] mb-1">Recuperar senha</h2>
    <p class="text-sm text-[#4a6b5a] mb-5">Informe seu CNPJ cadastrado.</p>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-[#4a6b5a] mb-1.5">CNPJ</label>
        <input type="text" placeholder="00.000.000/0001-00"
          class="w-full px-3 py-2.5 rounded-xl text-sm border bg-[#f8fcf8]" style="border-color:rgba(26,75,61,.2)">
      </div>
      <button class="btn-primary w-full py-2.5 rounded-xl font-semibold text-sm">
        Enviar link de recuperação
      </button>
    </div>
  </div>

  <p class="text-center text-xs text-[#4a6b5a] mt-6">
    &copy; <?= date('Y') ?> Prefeitura de Nova Lima · Portal Nexus
  </p>
</div>

<script>
function loginApp() {
  return {
    screen:     '<?= (!empty($error) || !empty($signup_success) || !empty($signup_error)) ? 'mei' : 'choice' ?>',
    cnpj:       '',
    sigCnpj:    '',
    sigTel:     '',
    showPw:     false,
    showServPw: false,

    maskCnpj(v) {
      v = v.replace(/\D/g,'').slice(0,14);
      return v.replace(/^(\d{2})(\d)/,'$1.$2')
              .replace(/^(\d{2})\.(\d{3})(\d)/,'$1.$2.$3')
              .replace(/\.(\d{3})(\d)/,'.$1/$2')
              .replace(/(\d{4})(\d)/,'$1-$2');
    },
    maskTel(v) {
      v = v.replace(/\D/g,'').slice(0,11);
      return v.replace(/^(\d{2})(\d)/,'($1) $2').replace(/(\d{5})(\d)/,'$1-$2');
    }
  }
}
</script>
</body>
</html>
