<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class m250820_094842_add_field_param_bigdata_system_id
 */
class m250820_094842_add_field_param_bigdata_system_id extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{params}}', 'chatbot_bigdata_system_id', Schema::TYPE_INTEGER. " DEFAULT 0");
        $this->addColumn('{{params}}', 'chatbot_bigdata_is_active', Schema::TYPE_SMALLINT. " NOT NULL DEFAULT 0");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('{{params}}', 'chatbot_bigdata_system_id');
        $this->dropColumn('{{params}}', 'chatbot_bigdata_is_active');
    }
}
