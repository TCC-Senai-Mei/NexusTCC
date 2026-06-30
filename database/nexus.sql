-- ============================================================
-- Portal Nexus — Sala do Empreendedor de Nova Lima
-- Schema e dados de exemplo
-- ============================================================

CREATE DATABASE IF NOT EXISTS nexus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexus;

-- ─────────────────────────────────────────────
-- TABELA: users
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) UNIQUE NOT NULL,
    password      VARCHAR(255) NOT NULL,
    role          ENUM('mei','servidor') NOT NULL,
    cnpj          VARCHAR(20)  UNIQUE NULL,
    nome_fantasia VARCHAR(100) NULL,
    telefone      VARCHAR(20)  NULL,
    atividade     VARCHAR(100) NULL,
    matricula     VARCHAR(20)  UNIQUE NULL,
    municipio     VARCHAR(100) DEFAULT 'Nova Lima, MG',
    situacao      VARCHAR(50)  DEFAULT 'Regular',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABELA: protocols
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS protocols (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    protocol_number VARCHAR(20) UNIQUE NOT NULL,
    user_id         INT NOT NULL,
    descricao       TEXT NOT NULL,
    categoria       VARCHAR(50) NOT NULL DEFAULT 'Geral',
    status          ENUM('Pendente','Em Análise','Resolvido') DEFAULT 'Pendente',
    canal           ENUM('Portal','Chatbot','Presencial') DEFAULT 'Portal',
    servidor_id     INT NULL,
    observacao      TEXT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABELA: documents
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS documents (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    tipo       VARCHAR(150) NOT NULL,
    status     VARCHAR(50)  NOT NULL,
    vencimento DATE NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABELA: notifications
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    title       VARCHAR(200) NOT NULL,
    description TEXT NULL,
    is_read     TINYINT(1) DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ============================================================
-- DADOS DE EXEMPLO (SEED)
-- Senhas: senha123 / prefeitura2025
-- ============================================================

-- Usuários
INSERT INTO users (name, email, password, role, cnpj, nome_fantasia, telefone, atividade, situacao) VALUES
('João Silva Barbosa',   'joao@jbeletrica.com',            '$2y$10$Wc38UUGuQJGC8bsUXjho3uZbng9XLYBa1jz4bx1ajFeQ/Zx/NGD5i', 'mei',      '12.345.678/0001-90', 'JB Elétrica',      '(31) 98765-4321', 'Instalações Elétricas',           'Regular'),
('Maria Aparecida Costa','mari.doceria@email.com',          '$2y$10$Wc38UUGuQJGC8bsUXjho3uZbng9XLYBa1jz4bx1ajFeQ/Zx/NGD5i', 'mei',      '98.765.432/0001-10', 'Doceria da Mari',  '(31) 97654-3210', 'Fabricação de Doces e Salgados', 'Regular'),
('Carlos Eduardo Lima',  'carlos.jardins@email.com',       '$2y$10$Wc38UUGuQJGC8bsUXjho3uZbng9XLYBa1jz4bx1ajFeQ/Zx/NGD5i', 'mei',      '55.123.456/0001-77', 'Jardins do Carlos', '(31) 96543-2100', 'Jardinagem e Paisagismo',         'Regular'),
('Ana Paula Ferreira',   'ana.paula@novalima.mg.gov.br',   '$2y$10$uXJBhVh9HIUHwdQLQr6P..zpukmW1lAV2.dy47.PkgK1hF4luVXbG', 'servidor', NULL,                NULL,               '(31) 3547-9000', NULL,                              'Regular');

-- Atualiza matrícula do servidor
UPDATE users SET matricula = '2025001' WHERE email = 'ana.paula@novalima.mg.gov.br';

-- Documentos do João (user_id=1)
INSERT INTO documents (user_id, tipo, status, vencimento) VALUES
(1, 'Declaração Anual (DASN-SIMEI 2025)', 'Atrasado',          '2025-05-31'),
(1, 'DAS — Junho/2025',                   'Pendente de Envio', '2025-06-20'),
(1, 'DAS — Maio/2025',                    'Regular',           '2025-05-20'),
(1, 'Alvará de Funcionamento',            'Regular',           '2025-12-31'),
(1, 'DAS — Abril/2025',                  'Regular',           '2025-04-20');

-- Documentos da Maria (user_id=2)
INSERT INTO documents (user_id, tipo, status, vencimento) VALUES
(2, 'Declaração Anual (DASN-SIMEI 2025)', 'Regular',  '2025-05-31'),
(2, 'DAS — Junho/2025',                   'Regular',  '2025-06-20'),
(2, 'Alvará de Funcionamento',            'Atrasado', '2025-04-30');

-- Documentos do Carlos (user_id=3)
INSERT INTO documents (user_id, tipo, status, vencimento) VALUES
(3, 'Declaração Anual (DASN-SIMEI 2025)', 'Regular',  '2025-05-31'),
(3, 'DAS — Junho/2025',                   'Regular',  '2025-06-20'),
(3, 'Alvará de Funcionamento',            'Regular',  '2025-12-31');

-- Protocolos do João (user_id=1)
INSERT INTO protocols (protocol_number, user_id, descricao, categoria, status, canal, servidor_id, observacao, created_at) VALUES
('NL-2025-04821', 1, 'Solicitação de Alvará de Funcionamento',    'Alvará',           'Em Análise', 'Portal',    4, 'Documentos recebidos. Aguardando vistoria técnica da equipe de fiscalização.', '2025-06-12 09:14:00'),
('NL-2025-03512', 1, 'Dúvida sobre prazo da Declaração Anual',    'Declaração Anual', 'Resolvido',  'Chatbot',   4, 'Atendido automaticamente pelo assistente virtual. Cliente orientado.',         '2025-05-28 14:32:00'),
('NL-2025-02887', 1, 'Regularização de DAS em atraso (Mar–Abr)', 'DAS',              'Resolvido',  'Presencial',4, 'Empreendedor compareceu à Sala do Empreendedor. Situação regularizada.',     '2025-04-10 11:00:00'),
('NL-2025-01887', 1, 'Atualização de endereço comercial',         'Cadastro',         'Resolvido',  'Presencial',4, 'Cadastro atualizado no sistema municipal. Novo alvará emitido.',             '2025-01-15 10:22:00');

-- Protocolos da Maria (user_id=2)
INSERT INTO protocols (protocol_number, user_id, descricao, categoria, status, canal, servidor_id, observacao, created_at) VALUES
('NL-2025-04312', 2, 'Renovação de Alvará de Funcionamento',  'Alvará',  'Em Análise', 'Portal',    4, 'Aguardando vistoria sanitária agendada para 25/06.',     '2025-06-08 16:45:00'),
('NL-2025-02201', 2, 'Inclusão de atividade secundária',      'Cadastro','Resolvido',  'Presencial',4, 'Atividade de confeitaria adicionada com sucesso.',       '2025-02-10 09:00:00');

-- Notificações do João (user_id=1)
INSERT INTO notifications (user_id, title, description, is_read, created_at) VALUES
(1, 'Documento pendente',      'Declaração Anual 2025 vence em 3 dias',     0, NOW()),
(1, 'Protocolo atualizado',    'NL-2025-04821 mudou para Em Análise',       0, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(1, 'DAS de junho disponível', 'Emita seu boleto antes do vencimento',      0, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(1, 'DAS de maio confirmado',  'Pagamento processado com sucesso',          1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'Bem-vindo ao Portal Nexus','Seu cadastro foi confirmado',              1, DATE_SUB(NOW(), INTERVAL 7 DAY));

-- Notificações do servidor Ana (user_id=4)
INSERT INTO notifications (user_id, title, description, is_read, created_at) VALUES
(4, 'Novo protocolo aberto',   'NL-2025-04821 aguarda análise',            0, NOW()),
(4, 'Vistoria agendada',       'Doceria da Mari — 25/06 às 10h',           0, DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- ============================================================
-- NOVAS TABELAS: CHAT COM PERSISTÊNCIA
-- ============================================================

-- ─────────────────────────────────────────────
-- TABELA: conversations (histórico de chats)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS conversations (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NOT NULL,
    servidor_id    INT NULL,
    titulo         VARCHAR(200) NOT NULL DEFAULT 'Nova conversa',
    tipo           ENUM('bot','humano') DEFAULT 'bot',
    status         ENUM('ativo','aguardando_servidor','em_atendimento','encerrado') DEFAULT 'ativo',
    protocol_number VARCHAR(20) NULL,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- ─────────────────────────────────────────────
-- TABELA: chat_messages (mensagens dos chats)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS chat_messages (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id       INT NULL,
    sender_role     ENUM('bot','mei','servidor') DEFAULT 'bot',
    message         TEXT NOT NULL,
    opcoes          TEXT NULL,
    protocol_number VARCHAR(20) NULL,
    is_read         TINYINT(1) DEFAULT 0,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id)
) ENGINE=InnoDB;

-- Senhas em texto simples (removida criptografia bcrypt)
UPDATE users SET password = 'senha123'      WHERE role = 'mei';
UPDATE users SET password = 'prefeitura2025' WHERE role = 'servidor';
