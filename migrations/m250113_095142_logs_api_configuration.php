<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;


class m250113_095142_logs_api_configuration extends Migration
{

    public function safeUp()
    {
        $this->createTable('logs_api_configuration', [

            'date_day'   => Schema::TYPE_DATE . ' DEFAULT NOW()',
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL ',
            'count_all' => Schema::TYPE_INTEGER ,//количество всех заходов в день
            'count_unic' => Schema::TYPE_INTEGER ,//количество уник заходов в день
            'json_users' => Schema::TYPE_JSONB ." NOT NULL DEFAULT '{}'::jsonb",//униеальный массив, для определения уникальности и ее подсчета {"0":124,"1":0,"2":56} ключ :  ид юзера в системе клиента
        ]);
        $this->createIndex(
            'idx_logs_api_configuration_date_day_pk',
            'logs_api_configuration',
            ['date_day', 'public_key'],
            true
        );
        $this->addForeignKey(
            'fk_logs_api_configuration_pk',
            'logs_api_configuration',
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
            'fk_logs_api_configuration_pk',
            'logs_api_configuration'
        );
        $this->dropIndex(
            'idx_logs_api_configuration_date_day_pk',
            'logs_api_configuration'
        );
        $this->dropTable('logs_api_configuration');
    }
}
