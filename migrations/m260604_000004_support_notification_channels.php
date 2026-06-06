<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260604_000004_support_notification_channels extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('support_settings', 'notify_email', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1');
        $this->addColumn('support_settings', 'notify_telegram', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->addColumn('support_settings', 'telegram_bot_token', Schema::TYPE_STRING . " NOT NULL DEFAULT ''");
        $this->addColumn('support_settings', 'telegram_chat_id', Schema::TYPE_STRING . " NOT NULL DEFAULT ''");
        $this->addColumn('support_settings', 'notify_max', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->addColumn('support_settings', 'max_api_url', Schema::TYPE_STRING . " NOT NULL DEFAULT 'https://platform-api.max.ru'");
        $this->addColumn('support_settings', 'max_bot_token', Schema::TYPE_STRING . " NOT NULL DEFAULT ''");
        $this->addColumn('support_settings', 'max_chat_id', Schema::TYPE_STRING . " NOT NULL DEFAULT ''");
        $this->db->schema->refreshTableSchema('support_settings');
    }

    public function safeDown(): void
    {
        $this->dropColumn('support_settings', 'max_chat_id');
        $this->dropColumn('support_settings', 'max_bot_token');
        $this->dropColumn('support_settings', 'max_api_url');
        $this->dropColumn('support_settings', 'notify_max');
        $this->dropColumn('support_settings', 'telegram_chat_id');
        $this->dropColumn('support_settings', 'telegram_bot_token');
        $this->dropColumn('support_settings', 'notify_telegram');
        $this->dropColumn('support_settings', 'notify_email');
        $this->db->schema->refreshTableSchema('support_settings');
    }
}
