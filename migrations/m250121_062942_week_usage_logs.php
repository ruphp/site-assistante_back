<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * function monday($date) {
 * $ts = strtotime($date);
 * $start = (date('w', $ts) == 1) ? $ts : strtotime('last monday', $ts);
 * return date('Y-m-d', $start);
 * }
 * echo monday(date('Y-m-d')); // понедельник текущей недели
 *
 * function sunday($date) {
 * $ts = strtotime($date);
 * $start = (date('w', $ts) == 1) ? $ts : strtotime('last monday', $ts);
 * return date('Y-m-d', strtotime('next sunday', $start));
 * }
 * echo sunday(date('Y-m-d')); // воскресенье текущей недели
 * ?>
 */
class m250121_062942_week_usage_logs extends Migration
{
    public function safeUp()
    {
        $this->createTable('week_usage_logs', [

            'monday_day'      => Schema::TYPE_DATE . ' NOT NULL ', // понедельник 'Y-m-d'
            'sunday_day'    => Schema::TYPE_DATE . ' NOT NULL ', // воскресенье 'Y-m-d'
            'public_key'  => Schema::TYPE_INTEGER . ' NOT NULL ',
            'type' => Schema::TYPE_STRING . ' NOT NULL ',
            'count_all'   => Schema::TYPE_INTEGER . ' NOT NULL ',
            'json_users'  => Schema::TYPE_JSONB . " NOT NULL DEFAULT '{}'::jsonb",
            'count_unic' => Schema::TYPE_INTEGER  . ' NOT NULL ',
            'json_roles_data' => Schema::TYPE_JSONB ." NOT NULL DEFAULT '{}'::jsonb",

        ]);

        $this->createIndex(
            'idx_courses_week_logs_data_id',
            'week_usage_logs',
            ['public_key','monday_day','sunday_day', 'type'],
            true
        );


    }

    public function safeDown()
    {

        $this->dropIndex(
            'idx_courses_week_logs_data_id',
            'week_usage_logs',
        );
        $this->dropTable('week_usage_logs');
    }
}
