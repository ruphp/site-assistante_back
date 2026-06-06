<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260605_000002_support_plan extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('support_settings', 'plan', Schema::TYPE_STRING . " NOT NULL DEFAULT 'free'");
        $this->db->schema->refreshTableSchema('support_settings');
    }

    public function safeDown(): void
    {
        $this->dropColumn('support_settings', 'plan');
        $this->db->schema->refreshTableSchema('support_settings');
    }
}
