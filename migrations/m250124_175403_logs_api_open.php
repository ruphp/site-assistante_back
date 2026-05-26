<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;


class m250124_175403_logs_api_open extends Migration
{

    public function safeUp()
    {
        $this->createTable('logs_api_open', [

            'date_day'   => Schema::TYPE_DATE . ' DEFAULT NOW()',
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL ',
            'count_all' => Schema::TYPE_INTEGER ,//количество всех заходов в день
            'count_unic' => Schema::TYPE_INTEGER ,//количество уник заходов в день
            'json_users' => Schema::TYPE_JSONB ." NOT NULL DEFAULT '{}'::jsonb",//униеальный массив, для определения уникальности и ее подсчета {"0":124,"1":0,"2":56} ключ :  ид юзера в системе клиента
            'json_roles_data' => Schema::TYPE_JSONB ." NOT NULL DEFAULT '{}'::jsonb",//массив,ролей и их подсчета каждого открытия {"8":23,"20":6,"13":56} id роли в системе клиента : количество открытий (итерируется)
        ]);
        $this->createIndex(
            'idx_logs_api_open_date_day_pk',
            'logs_api_open',
            ['date_day', 'public_key'],
            true
        );
        $this->addForeignKey(
            'fk_logs_api_open_pk',
            'logs_api_open',
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
            'fk_logs_api_open_pk',
            'logs_api_open'
        );
        $this->dropIndex(
            'idx_logs_api_open_date_day_pk',
            'logs_api_open'
        );
        $this->dropTable('logs_api_open');
    }
}
