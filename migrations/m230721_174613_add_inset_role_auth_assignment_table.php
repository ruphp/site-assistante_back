<?php

use yii\db\Migration;

/**
 * Class m230721_174613_add_inset_role_auth_assignment_table
 */
class m230721_174613_add_inset_role_auth_assignment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('auth_assignment', [
            'item_name' => 'admin',
            'user_id' => $_ENV['PK'],
            'created_at' => 'NOW()',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('auth_assignment', [
            'item_name' => 'manager',
            'user_id' => $_ENV['PK'],
            'created_at' => 'NOW()',
        ]);
    }
}
