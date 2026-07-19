<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260707_000001_support_auto_open_snooze extends Migration
{
    public function safeUp(): void
    {
        if ($this->db->getTableSchema('support_settings', true)?->getColumn('auto_open_snooze_minutes') === null) {
            $this->addColumn('support_settings', 'auto_open_snooze_minutes', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0');
        }

        $this->db->schema->refreshTableSchema('support_settings');
    }

    public function safeDown(): void
    {
        if ($this->db->getTableSchema('support_settings', true)?->getColumn('auto_open_snooze_minutes') !== null) {
            $this->dropColumn('support_settings', 'auto_open_snooze_minutes');
        }

        $this->db->schema->refreshTableSchema('support_settings');
    }
}
