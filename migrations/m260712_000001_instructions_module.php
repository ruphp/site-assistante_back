<?php

use yii\db\Migration;

final class m260712_000001_instructions_module extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%sw_instruction_categories}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->null(),
            'name' => $this->string(255)->notNull(),
            'sort_order' => $this->integer()->notNull()->defaultValue(100),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createTable('{{%sw_instruction_articles}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'category_id' => $this->integer()->null(),
            'title' => $this->string(255)->notNull(),
            'html' => $this->text()->notNull(),
            'sort_order' => $this->integer()->notNull()->defaultValue(100),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_sw_instruction_categories_public_key', '{{%sw_instruction_categories}}', 'public_key');
        $this->createIndex('idx_sw_instruction_categories_parent', '{{%sw_instruction_categories}}', 'parent_id');
        $this->createIndex('idx_sw_instruction_articles_public_key', '{{%sw_instruction_articles}}', 'public_key');
        $this->createIndex('idx_sw_instruction_articles_category', '{{%sw_instruction_articles}}', 'category_id');

        $this->addForeignKey(
            'fk_sw_instruction_categories_parent',
            '{{%sw_instruction_categories}}',
            'parent_id',
            '{{%sw_instruction_categories}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_sw_instruction_articles_category',
            '{{%sw_instruction_articles}}',
            'category_id',
            '{{%sw_instruction_categories}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_sw_instruction_articles_category', '{{%sw_instruction_articles}}');
        $this->dropForeignKey('fk_sw_instruction_categories_parent', '{{%sw_instruction_categories}}');
        $this->dropTable('{{%sw_instruction_articles}}');
        $this->dropTable('{{%sw_instruction_categories}}');
    }
}
