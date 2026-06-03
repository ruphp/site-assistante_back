<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class m250730_073003_params_add_select_designe_and_logo
 */
class m250730_073003_params_add_select_design_and_logo extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{params}}', 'design', Schema::TYPE_STRING. " NOT NULL DEFAULT 'dark'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('{{params}}', 'design');
    }

}
