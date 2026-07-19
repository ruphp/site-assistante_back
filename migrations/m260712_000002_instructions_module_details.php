<?php

use yii\db\Migration;

final class m260712_000002_instructions_module_details extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%sw_instruction_categories}}', 'admin_blocked', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%sw_instruction_categories}}', 'blocked_reason', $this->string(500)->null());

        $this->addColumn('{{%sw_instruction_articles}}', 'admin_blocked', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%sw_instruction_articles}}', 'blocked_reason', $this->string(500)->null());
        $this->addColumn('{{%sw_instruction_articles}}', 'views', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%sw_instruction_articles}}', 'likes', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%sw_instruction_articles}}', 'dislikes', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%sw_instruction_articles}}', 'content_bytes', $this->integer()->notNull()->defaultValue(0));

        $this->createTable('{{%sw_instruction_settings}}', [
            'public_key' => $this->integer()->notNull(),
            'creation_locked' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->addPrimaryKey('pk_sw_instruction_settings', '{{%sw_instruction_settings}}', 'public_key');

        $this->createTable('{{%sw_instruction_article_roles}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
        ]);
        $this->createIndex('idx_sw_instruction_article_roles_unique', '{{%sw_instruction_article_roles}}', ['article_id', 'role_id'], true);
        $this->addForeignKey('fk_sw_instruction_article_roles_article', '{{%sw_instruction_article_roles}}', 'article_id', '{{%sw_instruction_articles}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_instruction_article_roles_role', '{{%sw_instruction_article_roles}}', 'role_id', '{{%roles}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%sw_instruction_article_urls}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'public_key' => $this->integer()->notNull(),
            'url' => $this->string(500)->notNull(),
            'include_children' => $this->boolean()->notNull()->defaultValue(false),
            'include_query' => $this->boolean()->notNull()->defaultValue(false),
        ]);
        $this->createIndex('idx_sw_instruction_article_urls_article', '{{%sw_instruction_article_urls}}', 'article_id');
        $this->createIndex('idx_sw_instruction_article_urls_public_key', '{{%sw_instruction_article_urls}}', 'public_key');
        $this->addForeignKey('fk_sw_instruction_article_urls_article', '{{%sw_instruction_article_urls}}', 'article_id', '{{%sw_instruction_articles}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%sw_instruction_article_feedback}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'article_id' => $this->integer()->notNull(),
            'visitor_id' => $this->bigInteger()->notNull()->defaultValue(0),
            'visitor_key' => $this->string(255)->notNull()->defaultValue(''),
            'is_like' => $this->boolean()->notNull(),
            'comment' => $this->text()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_sw_instruction_article_feedback_article', '{{%sw_instruction_article_feedback}}', 'article_id');
        $this->createIndex('idx_sw_instruction_article_feedback_unique', '{{%sw_instruction_article_feedback}}', ['public_key', 'article_id', 'visitor_key'], true);
        $this->addForeignKey('fk_sw_instruction_article_feedback_article', '{{%sw_instruction_article_feedback}}', 'article_id', '{{%sw_instruction_articles}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%sw_instruction_article_favorites}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'article_id' => $this->integer()->notNull(),
            'visitor_id' => $this->bigInteger()->notNull()->defaultValue(0),
            'visitor_key' => $this->string(255)->notNull()->defaultValue(''),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
        $this->createIndex('idx_sw_instruction_article_favorites_unique', '{{%sw_instruction_article_favorites}}', ['public_key', 'article_id', 'visitor_key'], true);
        $this->addForeignKey('fk_sw_instruction_article_favorites_article', '{{%sw_instruction_article_favorites}}', 'article_id', '{{%sw_instruction_articles}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%sw_instruction_article_favorites}}');
        $this->dropTable('{{%sw_instruction_article_feedback}}');
        $this->dropTable('{{%sw_instruction_article_urls}}');
        $this->dropTable('{{%sw_instruction_article_roles}}');
        $this->dropTable('{{%sw_instruction_settings}}');

        $this->dropColumn('{{%sw_instruction_articles}}', 'content_bytes');
        $this->dropColumn('{{%sw_instruction_articles}}', 'dislikes');
        $this->dropColumn('{{%sw_instruction_articles}}', 'likes');
        $this->dropColumn('{{%sw_instruction_articles}}', 'views');
        $this->dropColumn('{{%sw_instruction_articles}}', 'blocked_reason');
        $this->dropColumn('{{%sw_instruction_articles}}', 'admin_blocked');

        $this->dropColumn('{{%sw_instruction_categories}}', 'blocked_reason');
        $this->dropColumn('{{%sw_instruction_categories}}', 'admin_blocked');
    }
}
