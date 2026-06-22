<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260621_000003_drop_support_settings_contact_info extends Migration
{
    public function safeUp()
    {
        if ($this->db->getTableSchema('support_settings', true)?->getColumn('contact_info') !== null) {
            $this->dropColumn('support_settings', 'contact_info');
        }
    }

    public function safeDown()
    {
        if ($this->db->getTableSchema('support_settings', true)?->getColumn('contact_info') === null) {
            $this->addColumn('support_settings', 'contact_info', Schema::TYPE_TEXT . " NOT NULL DEFAULT ''");
        }
    }
}
