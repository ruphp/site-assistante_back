<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260604_000006_support_work_schedule extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('support_settings', 'work_schedule', Schema::TYPE_JSONB . " NOT NULL DEFAULT '{}'::jsonb");
        $this->addColumn('support_settings', 'holiday_schedule', Schema::TYPE_JSONB . " NOT NULL DEFAULT '[]'::jsonb");
        $this->db->schema->refreshTableSchema('support_settings');
    }

    public function safeDown(): void
    {
        $this->dropColumn('support_settings', 'holiday_schedule');
        $this->dropColumn('support_settings', 'work_schedule');
        $this->db->schema->refreshTableSchema('support_settings');
    }
}
