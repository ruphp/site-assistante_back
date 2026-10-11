<?php

use yii\db\Migration;

final class m261012_000001_support_entry_point_instruction extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('support_entry_points', 'instruction_id', $this->integer()->null());
        $this->createIndex(
            'idx_support_entry_points_instruction',
            'support_entry_points',
            'instruction_id',
        );
        $this->addForeignKey(
            'fk_support_entry_points_instruction',
            'support_entry_points',
            'instruction_id',
            '{{%sw_instruction_articles}}',
            'id',
            'SET NULL',
            'CASCADE',
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_support_entry_points_instruction', 'support_entry_points');
        $this->dropIndex('idx_support_entry_points_instruction', 'support_entry_points');
        $this->dropColumn('support_entry_points', 'instruction_id');
    }
}
