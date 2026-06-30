<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatTables extends Migration
{
    public function up()
    {
        // Tabela de Sessões (Conversas)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'data_criacao' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('chat_sessions', true);

        // Tabela de Mensagens
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'texto' => [
                'type' => 'TEXT',
            ],
            'remetente' => [
                'type'       => 'ENUM',
                'constraint' => ['user', 'bot', 'servidor'],
                'default'    => 'user',
            ],
            'timestamp' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'protocolo_gerado' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'opcoes_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('session_id', 'chat_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('chat_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('chat_messages', true);
        $this->forge->dropTable('chat_sessions', true);
    }
}