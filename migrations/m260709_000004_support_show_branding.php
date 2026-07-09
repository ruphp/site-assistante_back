<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260709_000004_support_show_branding extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('support_settings', 'show_branding', Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT TRUE');
        $this->db->schema->refreshTableSchema('support_settings');
    }

    public function safeDown(): void
    {
        $this->dropColumn('support_settings', 'show_branding');
        $this->db->schema->refreshTableSchema('support_settings');
    }
}
