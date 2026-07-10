<?php

use yii\db\Migration;

class m260710_000001_support_telegram_manager_bot extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('support_telegram_manager_links', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'public_key' => $this->integer()->notNull(),
            'chat_id' => $this->string(64)->notNull(),
            'telegram_user_id' => $this->string(64)->notNull(),
            'username' => $this->string(255)->null(),
            'first_name' => $this->string(255)->null(),
            'last_name' => $this->string(255)->null(),
            'pending_conversation_id' => $this->integer()->null(),
            'is_active' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_support_tg_links_user', 'support_telegram_manager_links', ['user_id']);
        $this->createIndex('idx_support_tg_links_client', 'support_telegram_manager_links', ['public_key', 'is_active']);
        $this->createIndex('idx_support_tg_links_chat', 'support_telegram_manager_links', ['chat_id'], true);

        $this->createTable('support_telegram_manager_codes', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'public_key' => $this->integer()->notNull(),
            'code' => $this->string(32)->notNull(),
            'expires_at' => $this->timestamp()->notNull(),
            'used_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_support_tg_codes_code', 'support_telegram_manager_codes', ['code'], true);
        $this->createIndex('idx_support_tg_codes_user', 'support_telegram_manager_codes', ['user_id', 'public_key']);
    }

    public function safeDown(): void
    {
        $this->dropTable('support_telegram_manager_codes');
        $this->dropTable('support_telegram_manager_links');
    }
}
