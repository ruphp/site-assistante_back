<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

/**
 * Class m250121_062943_day_usage_logs
 */
class m250121_062941_day_usage_logs extends Migration
{
    public function safeUp()
    {
        $this->createTable('day_usage_logs', [

            'date_day'   => Schema::TYPE_DATE . ' DEFAULT NOW()',
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL ',
            'type' => Schema::TYPE_STRING . ' NOT NULL ',
            'count_all' => Schema::TYPE_INTEGER  . ' NOT NULL ',
            'json_users' => Schema::TYPE_JSONB ." NOT NULL DEFAULT '{}'::jsonb",
            'count_unic' => Schema::TYPE_INTEGER  . ' NOT NULL ',
            'json_roles_data' => Schema::TYPE_JSONB ." NOT NULL DEFAULT '{}'::jsonb",


        ]);

        $this->createIndex(
            'idx_courses_day_logs_pk_data_id',
            'day_usage_logs',
            ['public_key','date_day', 'type'],
            true
        );


    }


    public function safeDown()
    {

        $this->dropIndex(
            'idx_courses_day_logs_pk_data_id',
            'day_usage_logs',
        );
        $this->dropTable('day_usage_logs');
    }
}
