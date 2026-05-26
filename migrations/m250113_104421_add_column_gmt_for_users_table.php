<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class m250113_104421_add_column_gmt_for_params_table
 */
class m250113_104421_add_column_gmt_for_users_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{users}}', 'gmt', Schema::TYPE_STRING . " NOT NULL DEFAULT '+5'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('{{users}}', 'gmt');
    }
}
