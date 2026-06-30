# Portal Nexus — CodeIgniter 4
### Sistema de Atendimento ao MEI — Sala do Empreendedor de Nova Lima

---

## Visão Geral

O **Portal Nexus** é um sistema web desenvolvido em **PHP com CodeIgniter 4**
para a Sala do Empreendedor do município de Nova Lima (MG). A plataforma atende
dois perfis de usuário:

- **MEI** – Microempreendedor Individual
- **Servidor** – Funcionário da Prefeitura responsável pelo atendimento

### Funcionalidades
- Autenticação separada por perfil (MEI e Servidor)
- Cadastro de novos MEIs
- Dashboard com indicadores e gráficos
- Portal MEI com gestão de documentos e protocolos
- Painel do Servidor com gerenciamento de atendimentos
- Assistente Virtual (Chatbot com FAQ sobre MEI)
- Histórico completo de protocolos com filtros
- Configurações de perfil e senha

---

## Pré-requisitos

| Ferramenta | Versão mínima |
|-----------|--------------|
| PHP        | 7.4+         |
| MySQL      | 5.7+         |
| Apache / Nginx | qualquer |
| Composer   | 2.x (opcional, só se for reinstalar dependências) |

---

## Instalação — Passo a Passo

### 1. Prepare o CodeIgniter base

```
1. Extraia o arquivo codeigniter.zip em seu servidor:
   - XAMPP: C:/xampp/htdocs/nexus-ci4/
   - Linux Apache: /var/www/html/nexus-ci4/

2. Garanta que a pasta /nexus-ci4/public/ seja a raiz pública
```

### 2. Copie os arquivos deste pacote

```
Copie o conteúdo da pasta app/ para dentro de nexus-ci4/app/
(substitua os arquivos se necessário — especialmente Config/Routes.php e Config/Filters.php)
```

### 3. Configure o banco de dados

```sql
-- Acesse o phpMyAdmin ou MySQL CLI e execute:
mysql -u root -p < database/nexus.sql
```

Ou abra o arquivo `database/nexus.sql` no phpMyAdmin e execute.

### 4. Configure o arquivo .env

```bash
# Copie o arquivo de exemplo
cp .env.example .env

# Edite o .env com suas configurações:
```

Principais configurações a alterar no `.env`:

```env
# URL da sua aplicação
app.baseURL = 'http://localhost/nexus-ci4/public'

# Banco de dados
database.default.hostname = localhost
database.default.database = nexus
database.default.username = root
database.default.password = SUA_SENHA_AQUI
```

### 5. Gere a chave de criptografia

```bash
# No terminal, dentro da pasta nexus-ci4:
php spark key:generate
```

### 6. Permissões (Linux/Mac)

```bash
chmod -R 755 nexus-ci4/
chmod -R 777 nexus-ci4/writable/
```

### 7. Configure o Apache (se necessário)

Verifique se o `mod_rewrite` está ativo. O arquivo `public/.htaccess` já está
configurado no CodeIgniter padrão.

Para XAMPP, certifique-se que `AllowOverride All` está ativo no `httpd.conf`.

---

## Acessando o Sistema

Abra o navegador em: **http://localhost/nexus-ci4/public**

### Contas de demonstração

| Perfil   | Credencial              | Senha           |
|----------|------------------------|-----------------|
| MEI      | CNPJ: 12.345.678/0001-90 | senha123       |
| MEI      | CNPJ: 98.765.432/0001-10 | senha123       |
| Servidor | Matrícula: 2025001      | prefeitura2025  |

---

## Estrutura do Projeto

```
nexus-ci4/
├── app/
│   ├── Config/
│   │   ├── Routes.php          ← Rotas da aplicação
│   │   └── Filters.php         ← Registro do filtro de autenticação
│   ├── Controllers/
│   │   ├── Auth.php            ← Login, Cadastro, Logout
│   │   ├── Dashboard.php       ← Dashboard principal
│   │   ├── Chat.php            ← Assistente virtual / Chatbot
│   │   ├── Portal.php          ← Portal MEI e Painel Servidor
│   │   ├── Protocols.php       ← Listagem de protocolos
│   │   └── Settings.php        ← Configurações de usuário
│   ├── Filters/
│   │   └── AuthFilter.php      ← Proteção de rotas autenticadas
│   ├── Models/
│   │   ├── UserModel.php       ← Model de usuários
│   │   ├── ProtocolModel.php   ← Model de protocolos
│   │   ├── DocumentModel.php   ← Model de documentos
│   │   └── NotificationModel.php ← Model de notificações
│   └── Views/
│       ├── layouts/
│       │   └── main.php        ← Layout com sidebar + header
│       ├── auth/
│       │   └── login.php       ← Tela de login
│       ├── dashboard/
│       │   ├── index_mei.php   ← Dashboard do MEI
│       │   └── index_servidor.php ← Dashboard do Servidor
│       ├── chat/
│       │   └── index.php       ← Assistente virtual
│       ├── portal/
│       │   ├── mei.php         ← Portal do MEI
│       │   └── server.php      ← Painel do Servidor
│       ├── protocols/
│       │   └── index.php       ← Lista de protocolos
│       └── settings/
│           └── index.php       ← Configurações
└── database/
    └── nexus.sql               ← Schema + dados de exemplo
```

---

## Tecnologias Utilizadas

| Tecnologia      | Uso                          |
|----------------|------------------------------|
| PHP 7.4+        | Backend                      |
| CodeIgniter 4   | Framework MVC                |
| MySQL           | Banco de dados               |
| Tailwind CSS    | Estilização (via CDN)        |
| Alpine.js       | Interatividade frontend      |
| Chart.js        | Gráficos do dashboard        |
| Bootstrap Icons | Ícones da interface          |

---

## Padrão MVC — CodeIgniter 4

```
URL → Routes.php → Controller → Model → View
                      ↓
                  Filters (autenticação)
```

### Exemplo de fluxo — Login MEI:

1. `GET /login` → `Auth::index()` → renderiza `auth/login.php`
2. Usuário preenche CNPJ e senha → `POST /auth/mei`
3. `Auth::meiLogin()` → consulta `UserModel` → verifica senha
4. Se válido → salva sessão → redireciona para `/dashboard`
5. `AuthFilter` protege todas as rotas do dashboard

---

## Problemas Comuns

**Erro 404 em todas as páginas:**
> Verifique se o mod_rewrite está ativo e se o baseURL no .env está correto.

**Erro de conexão com banco:**
> Confira host, database, username e password no .env.

**Tela branca (PHP error):**
> Mude `CI_ENVIRONMENT = development` no .env para ver os erros detalhados.

**Sessão não persiste:**
> Verifique se a pasta `writable/session/` tem permissão de escrita.

---

## Desenvolvido para TCC — SENAI

Sistema desenvolvido como Trabalho de Conclusão de Curso (TCC) para o
**SENAI**, com o tema: Portal de Atendimento Digital ao MEI para a
Sala do Empreendedor do Município de Nova Lima - MG.
