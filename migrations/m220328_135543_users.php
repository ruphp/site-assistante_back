<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class m220328_135543_users
 */
class m220328_135543_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'email' => Schema::TYPE_STRING. ' NOT NULL',
            'passhash' => Schema::TYPE_STRING. ' NOT NULL', 
            'status' => Schema::TYPE_SMALLINT. ' DEFAULT 1',
            'firm' => Schema::TYPE_TEXT. ' NOT NULL',
            'public_key' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
        ]);
        $this->createIndex(
            'idx_users_pk',
            'users',
            'public_key'
        );
        $this->createIndex(
            'unic_user_pk_id',
            'users',
            ['id','public_key'],
            true
        );
        $this->addForeignKey(
            'fk_users_pk',
            'users',
            'public_key',
            'users',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_users_pk',
            'users'
        );
        $this->dropIndex(
            'unic_user_pk_id',
            'users'
        );
        $this->dropIndex(
            'idx_users_pk',
            'users'
        );
        $this->dropTable('users');
    }
}
