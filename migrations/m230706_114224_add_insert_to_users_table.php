<?php

use yii\db\Migration;

/**
 * Class m230706_114224_add_insert_to_users_table
 */
class m230706_114224_add_insert_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('users', [
            'name' => $_ENV['ADMIN_NAME'],
            'email' => $_ENV['ADMIN_EMAIL'],
            'passhash' => Yii::$app->security->generatePasswordHash($_ENV['ADMIN_PASS']),
            'firm' => $_ENV['ADMIN_FIRM'],
            'public_key' => $_ENV['PK'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('users', ['email' => $_ENV['ADMIN_EMAIL']]);
    }
}
