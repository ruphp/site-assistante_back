<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

final class m260719_000002_fix_support_settings_schema extends Migration
{
    private string $table = 'support_settings';

    public function safeUp(): void
    {
        if ($this->db->getTableSchema($this->table, true) === null) {
            return;
        }

        $this->addColumnIfMissing('title', Schema::TYPE_STRING . " NOT NULL DEFAULT 'Онлайн-поддержка'");
        $this->addColumnIfMissing('contact_info', Schema::TYPE_TEXT . " NOT NULL DEFAULT ''");
        $this->addColumnIfMissing('timezone', Schema::TYPE_STRING . " NOT NULL DEFAULT 'Asia/Yekaterinburg'");
        $this->addColumnIfMissing('working_hours', Schema::TYPE_TEXT . " NOT NULL DEFAULT 'Пн-Пт 09:00-18:00'");
        $this->addColumnIfMissing('ask_name', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->addColumnIfMissing('ask_email', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1');
        $this->addColumnIfMissing('ask_phone', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->addColumnIfMissing('require_email_offline', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1');
        $this->addColumnIfMissing('auto_reply', Schema::TYPE_TEXT . " NOT NULL DEFAULT 'Спасибо, мы получили сообщение.'");
        $this->addColumnIfMissing('notify_email', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1');
        $this->addColumnIfMissing('notify_telegram', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->addColumnIfMissing('telegram_bot_token', Schema::TYPE_STRING . " NOT NULL DEFAULT ''");
        $this->addColumnIfMissing('telegram_chat_id', Schema::TYPE_STRING . " NOT NULL DEFAULT ''");
        $this->addColumnIfMissing('notify_max', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->addColumnIfMissing('max_api_url', Schema::TYPE_STRING . " NOT NULL DEFAULT 'https://platform-api.max.ru'");
        $this->addColumnIfMissing('max_bot_token', Schema::TYPE_STRING . " NOT NULL DEFAULT ''");
        $this->addColumnIfMissing('max_chat_id', Schema::TYPE_STRING . " NOT NULL DEFAULT ''");
        $this->addColumnIfMissing('notification_emails', Schema::TYPE_TEXT . " NOT NULL DEFAULT ''");
        $this->addColumnIfMissing('work_schedule', Schema::TYPE_JSONB . " NOT NULL DEFAULT '{}'::jsonb");
        $this->addColumnIfMissing('holiday_schedule', Schema::TYPE_JSONB . " NOT NULL DEFAULT '[]'::jsonb");
        $this->addColumnIfMissing('plan', Schema::TYPE_STRING . " NOT NULL DEFAULT 'free'");

        $this->db->schema->refreshTableSchema($this->table);
    }

    public function safeDown(): void
    {
        echo "m260719_000002_fix_support_settings_schema is a corrective schema migration and cannot be reverted safely.\n";
    }

    private function addColumnIfMissing(string $column, string $type): void
    {
        $schema = $this->db->getTableSchema($this->table, true);
        if ($schema !== null && $schema->getColumn($column) === null) {
            $this->addColumn($this->table, $column, $type);
        }
    }
}
