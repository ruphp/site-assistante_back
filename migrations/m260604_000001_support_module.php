<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260604_000001_support_module extends Migration
{
    public function safeUp()
    {
        $this->createTable('support_settings', [
            'id' => Schema::TYPE_PK,
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'enabled' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            'welcome_message' => Schema::TYPE_TEXT . " NOT NULL DEFAULT 'Здравствуйте! Напишите нам, мы поможем.'",
            'offline_message' => Schema::TYPE_TEXT . " NOT NULL DEFAULT 'Мы сейчас не онлайн, но ответим позже.'",
            'polling_interval_seconds' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 5',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
        ]);
        $this->createIndex('idx_support_settings_public_key', 'support_settings', 'public_key', true);
        $this->addForeignKey(
            'fk_support_settings_public_key',
            'support_settings',
            ['public_key', 'public_key'],
            'users',
            ['id', 'public_key'],
            'CASCADE',
        );

        $this->createTable('support_conversations', [
            'id' => Schema::TYPE_PK,
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'visitor_id' => Schema::TYPE_STRING . ' NOT NULL',
            'visitor_ip' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'status' => Schema::TYPE_STRING . " NOT NULL DEFAULT 'open'",
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
            'closed_at' => Schema::TYPE_TIMESTAMP . ' DEFAULT NULL',
        ]);
        $this->createIndex('idx_support_conversations_client_visitor', 'support_conversations', ['public_key', 'visitor_id']);
        $this->createIndex('idx_support_conversations_client_status', 'support_conversations', ['public_key', 'status']);
        $this->addForeignKey(
            'fk_support_conversations_public_key',
            'support_conversations',
            ['public_key', 'public_key'],
            'users',
            ['id', 'public_key'],
            'CASCADE',
        );

        $this->createTable('support_messages', [
            'id' => Schema::TYPE_PK,
            'conversation_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'sender_type' => Schema::TYPE_STRING . ' NOT NULL',
            'sender_id' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'body' => Schema::TYPE_TEXT . ' NOT NULL',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
        ]);
        $this->createIndex('idx_support_messages_conversation', 'support_messages', ['conversation_id', 'id']);
        $this->createIndex('idx_support_messages_public_key', 'support_messages', 'public_key');
        $this->addForeignKey(
            'fk_support_messages_conversation',
            'support_messages',
            'conversation_id',
            'support_conversations',
            'id',
            'CASCADE',
        );

        $this->createTable('support_usage_month', [
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'period_month' => Schema::TYPE_DATE . ' NOT NULL',
            'conversation_count' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'message_count' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
        ]);
        $this->addPrimaryKey('pk_support_usage_month', 'support_usage_month', ['public_key', 'period_month']);
        $this->addForeignKey(
            'fk_support_usage_month_public_key',
            'support_usage_month',
            ['public_key', 'public_key'],
            'users',
            ['id', 'public_key'],
            'CASCADE',
        );

        $this->insertSupportPermission();
        $this->grantSupportToExistingClients();
    }

    public function safeDown()
    {
        $this->delete('auth_assignment', ['item_name' => 'support']);
        $this->delete('auth_item_child', ['parent' => 'accesses_modules', 'child' => 'support']);
        $this->delete('auth_item', ['name' => 'support']);

        $this->dropForeignKey('fk_support_usage_month_public_key', 'support_usage_month');
        $this->dropPrimaryKey('pk_support_usage_month', 'support_usage_month');
        $this->dropTable('support_usage_month');

        $this->dropForeignKey('fk_support_messages_conversation', 'support_messages');
        $this->dropIndex('idx_support_messages_public_key', 'support_messages');
        $this->dropIndex('idx_support_messages_conversation', 'support_messages');
        $this->dropTable('support_messages');

        $this->dropForeignKey('fk_support_conversations_public_key', 'support_conversations');
        $this->dropIndex('idx_support_conversations_client_status', 'support_conversations');
        $this->dropIndex('idx_support_conversations_client_visitor', 'support_conversations');
        $this->dropTable('support_conversations');

        $this->dropForeignKey('fk_support_settings_public_key', 'support_settings');
        $this->dropIndex('idx_support_settings_public_key', 'support_settings');
        $this->dropTable('support_settings');
    }

    private function insertSupportPermission(): void
    {
        if ($this->db->createCommand("SELECT 1 FROM auth_item WHERE name = 'support'")->queryScalar()) {
            return;
        }

        $this->insert('auth_item', [
            'name' => 'support',
            'type' => 2,
            'description' => 'Онлайн-поддержка',
            'data' => null,
        ]);
        $this->insert('auth_item_child', [
            'parent' => 'accesses_modules',
            'child' => 'support',
        ]);
    }

    private function grantSupportToExistingClients(): void
    {
        $this->execute(
            "INSERT INTO auth_assignment (item_name, user_id, created_at)
                SELECT 'support', users.id, EXTRACT(EPOCH FROM NOW())::integer
                FROM users
                INNER JOIN auth_assignment manager_role
                    ON manager_role.user_id = users.id AND manager_role.item_name = 'manager'
                ON CONFLICT DO NOTHING",
        );
    }
}
