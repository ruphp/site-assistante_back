<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260709_000003_support_entry_point_response_type extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn(
            'support_entry_points',
            'response_type',
            Schema::TYPE_STRING . " NOT NULL DEFAULT 'answer'",
        );
    }

    public function safeDown(): void
    {
        $this->dropColumn('support_entry_points', 'response_type');
    }
}
