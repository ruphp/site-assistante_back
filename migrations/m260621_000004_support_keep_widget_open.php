<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260621_000004_support_keep_widget_open extends Migration
{
    public function safeUp(): void
    {
        if ($this->db->getTableSchema('support_settings', true)?->getColumn('keep_widget_open_when_online') === null) {
            $this->addColumn('support_settings', 'keep_widget_open_when_online', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1');
        }

        $this->db->schema->refreshTableSchema('support_settings');
    }

    public function safeDown(): void
    {
        if ($this->db->getTableSchema('support_settings', true)?->getColumn('keep_widget_open_when_online') !== null) {
            $this->dropColumn('support_settings', 'keep_widget_open_when_online');
        }

        $this->db->schema->refreshTableSchema('support_settings');
    }
}
