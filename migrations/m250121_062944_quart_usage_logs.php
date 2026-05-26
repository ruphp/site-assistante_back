<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * function startKv($d){
 * $kv = (int)((date('n', $d)-1)/3+1);
 * $year = date('y', $d);
 * return date('Y-m-d',mktime(0,0,0,($kv-1)*3+1,1,$year));
 * }
 *
 * function endKv($d){
 * $kv = (int)((date('n', $d)-1)/3+1);
 * $year = date('y', $d);
 * return date('Y-m-d',mktime(0,0,0,($kv)*3+1,0,$year));
 * }
 *
 * echo startKv(mktime(0,0,0, date('m'), date('d'), date('Y')));
 *
 * echo endKv(mktime(0,0,0, date('m'), date('d'), date('Y')));
 */
class m250121_062944_quart_usage_logs extends Migration
{
    public function safeUp()
    {
        $this->createTable('quart_usage_logs', [

            'first_quart_day'    => Schema::TYPE_DATE . ' NOT NULL ',// первый день квартала
            'last_quart_day'    => Schema::TYPE_DATE . ' NOT NULL ', // последний день квартала
            'public_key'  => Schema::TYPE_INTEGER . ' NOT NULL ',
            'type' => Schema::TYPE_STRING . ' NOT NULL ',
            'count_all'   => Schema::TYPE_INTEGER . ' NOT NULL ',
            'json_users'  => Schema::TYPE_JSONB . " NOT NULL DEFAULT '{}'::jsonb",
            'count_unic' => Schema::TYPE_INTEGER  . ' NOT NULL ',
            'json_roles_data' => Schema::TYPE_JSONB ." NOT NULL DEFAULT '{}'::jsonb",

        ]);

        $this->createIndex(
            'idx_courses_quart_logs_data_id',
            'quart_usage_logs',
            ['public_key','first_quart_day','last_quart_day','type'],
            true
        );


    }

    public function safeDown()
    {

        $this->dropIndex(
            'idx_courses_quart_logs_data_id',
            'quart_usage_logs',
        );
        $this->dropTable('quart_usage_logs');
    }
}
