<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m220328_144340_params extends Migration
{
    /**
     * {@inheritdoc}
     * tab_tickets - '0-не ипользовать чат с тп; 1-использовать встроенную смартиус ТП; 2-исп интеграцию с суи',
     */
    public function safeUp()
    {
        $this->createTable('params', [
            'id' => Schema::TYPE_PK,
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'color' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'domain' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'run' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            'tab_tickets' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'tab_tp_contacts' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'tp_contacts' => Schema::TYPE_TEXT,
            'default_answer' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'leftbutton' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'timeout' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'server_stp' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'token_stp' => Schema::TYPE_STRING . ' DEFAULT NULL',
            'is_uuid' => Schema::TYPE_SMALLINT . ' DEFAULT 0 NOT NULL',
        ]);
        $this->createIndex(
            'idx_params_pk',
            'params',
            'public_key'
        );

        $this->addForeignKey(
            'fk_params_pk',
            'params',
            ['public_key', 'public_key'],
            'users',
            ['id', 'public_key'],
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_params_pk',
            'params'
        );
        $this->dropIndex(
            'idx_params_pk',
            'params'
        );
        $this->dropTable('params');
    }
}
