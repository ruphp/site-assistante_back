<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260605_000001_support_entry_points extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('support_entry_points', [
            'id' => Schema::TYPE_PK,
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT . " NOT NULL DEFAULT ''",
            'priority' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            'enabled' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            'sort_order' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 100',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
        ]);
        $this->createIndex('idx_support_entry_points_client_enabled', 'support_entry_points', ['public_key', 'enabled', 'sort_order']);
        $this->addForeignKey(
            'fk_support_entry_points_public_key',
            'support_entry_points',
            ['public_key', 'public_key'],
            'users',
            ['id', 'public_key'],
            'CASCADE',
        );

        $this->addColumn('support_conversations', 'entry_point_id', Schema::TYPE_INTEGER . ' DEFAULT NULL');
        $this->addColumn('support_conversations', 'priority', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0');
        $this->createIndex('idx_support_conversations_client_priority', 'support_conversations', ['public_key', 'priority']);
        $this->addForeignKey(
            'fk_support_conversations_entry_point',
            'support_conversations',
            'entry_point_id',
            'support_entry_points',
            'id',
            'SET NULL',
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_support_conversations_entry_point', 'support_conversations');
        $this->dropIndex('idx_support_conversations_client_priority', 'support_conversations');
        $this->dropColumn('support_conversations', 'priority');
        $this->dropColumn('support_conversations', 'entry_point_id');

        $this->dropForeignKey('fk_support_entry_points_public_key', 'support_entry_points');
        $this->dropIndex('idx_support_entry_points_client_enabled', 'support_entry_points');
        $this->dropTable('support_entry_points');
    }
}
