<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260604_000005_support_notification_emails extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('support_settings', 'notification_emails', Schema::TYPE_TEXT . " NOT NULL DEFAULT ''");
        $this->db->schema->refreshTableSchema('support_settings');
    }

    public function safeDown(): void
    {
        $this->dropColumn('support_settings', 'notification_emails');
        $this->db->schema->refreshTableSchema('support_settings');
    }
}
