<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class _m_250121063212_year_usage_logs
 */
class m250121_063212_year_usage_logs extends Migration
{
    public function safeUp()
    {
        $this->createTable('year_usage_logs', [

            'first_day'    => Schema::TYPE_DATE . ' NOT NULL ', // date('Y-01-01')
            'public_key'  => Schema::TYPE_INTEGER . ' NOT NULL ',
            'type' => Schema::TYPE_STRING . ' NOT NULL ',
            'count_all'   => Schema::TYPE_INTEGER . ' NOT NULL ',
            'json_users'  => Schema::TYPE_JSONB . " NOT NULL DEFAULT '{}'::jsonb",
            'count_unic' => Schema::TYPE_INTEGER  . ' NOT NULL ',
            'json_roles_data' => Schema::TYPE_JSONB ." NOT NULL DEFAULT '{}'::jsonb",

        ]);
        $this->createIndex(
            'idx_courses_year_logs_data_id',
            'year_usage_logs',
            ['public_key','first_day', 'type'],
            true
        );


    }

    public function safeDown()
    {

        $this->dropIndex(
            'idx_courses_year_logs_data_id',
            'year_usage_logs',
        );
        $this->dropTable('year_usage_logs');
    }
}
