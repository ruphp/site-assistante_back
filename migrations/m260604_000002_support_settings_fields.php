<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260604_000002_support_settings_fields extends Migration
{
    public function safeUp()
    {
        $this->addColumn('support_settings', 'title', Schema::TYPE_STRING . " NOT NULL DEFAULT 'Онлайн-поддержка'");
        $this->addColumn('support_settings', 'contact_info', Schema::TYPE_TEXT . " NOT NULL DEFAULT ''");
        $this->addColumn('support_settings', 'timezone', Schema::TYPE_STRING . " NOT NULL DEFAULT 'Asia/Yekaterinburg'");
        $this->addColumn('support_settings', 'working_hours', Schema::TYPE_TEXT . " NOT NULL DEFAULT 'Пн-Пт 09:00-18:00'");
        $this->addColumn('support_settings', 'ask_name', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->addColumn('support_settings', 'ask_email', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1');
        $this->addColumn('support_settings', 'ask_phone', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->addColumn('support_settings', 'require_email_offline', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1');
        $this->addColumn('support_settings', 'auto_reply', Schema::TYPE_TEXT . " NOT NULL DEFAULT 'Спасибо, мы получили сообщение.'");
    }

    public function safeDown()
    {
        $this->dropColumn('support_settings', 'auto_reply');
        $this->dropColumn('support_settings', 'require_email_offline');
        $this->dropColumn('support_settings', 'ask_phone');
        $this->dropColumn('support_settings', 'ask_email');
        $this->dropColumn('support_settings', 'ask_name');
        $this->dropColumn('support_settings', 'working_hours');
        $this->dropColumn('support_settings', 'timezone');
        $this->dropColumn('support_settings', 'contact_info');
        $this->dropColumn('support_settings', 'title');
    }
}
