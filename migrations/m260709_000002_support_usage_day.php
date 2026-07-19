<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260709_000002_support_usage_day extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('support_usage_day', [
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'period_day' => Schema::TYPE_DATE . ' NOT NULL',
            'operator_reply_count' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
        ]);

        $this->addPrimaryKey('pk_support_usage_day', 'support_usage_day', ['public_key', 'period_day']);
        $this->createIndex('idx_support_usage_day_public_key', 'support_usage_day', 'public_key');
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx_support_usage_day_public_key', 'support_usage_day');
        $this->dropPrimaryKey('pk_support_usage_day', 'support_usage_day');
        $this->dropTable('support_usage_day');
    }
}
