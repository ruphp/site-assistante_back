<?php

use yii\db\Migration;

final class m260721_000001_surveys_module extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%sw_survey_forms}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'is_important' => $this->boolean()->notNull()->defaultValue(false),
            'question_count' => $this->integer()->null(),
            'date_start' => $this->date()->null(),
            'date_finish' => $this->date()->null(),
            'sort_order' => $this->integer()->notNull()->defaultValue(100),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createTable('{{%sw_survey_questions}}', [
            'id' => $this->primaryKey(),
            'survey_id' => $this->integer()->notNull(),
            'type' => $this->string(32)->notNull()->defaultValue('text'),
            'title' => $this->text()->notNull(),
            'is_required' => $this->boolean()->notNull()->defaultValue(false),
            'is_free_answer' => $this->boolean()->notNull()->defaultValue(false),
            'sort_order' => $this->integer()->notNull()->defaultValue(100),
        ]);

        $this->createTable('{{%sw_survey_question_options}}', [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'answer' => $this->string(500)->notNull(),
            'sort_order' => $this->integer()->notNull()->defaultValue(100),
        ]);

        $this->createTable('{{%sw_survey_urls}}', [
            'id' => $this->primaryKey(),
            'survey_id' => $this->integer()->notNull(),
            'public_key' => $this->integer()->notNull(),
            'url' => $this->string(500)->notNull(),
            'include_children' => $this->boolean()->notNull()->defaultValue(false),
            'include_query' => $this->boolean()->notNull()->defaultValue(false),
        ]);

        $this->createTable('{{%sw_survey_roles}}', [
            'id' => $this->primaryKey(),
            'survey_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%sw_survey_responses}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'survey_id' => $this->integer()->notNull(),
            'visitor_key' => $this->string(255)->notNull(),
            'visitor_id' => $this->string(255)->null(),
            'user_id' => $this->integer()->null(),
            'roles_json' => 'jsonb NOT NULL DEFAULT \'[]\'::jsonb',
            'answers_json' => 'jsonb NOT NULL DEFAULT \'[]\'::jsonb',
            'is_delayed' => $this->boolean()->notNull()->defaultValue(false),
            'completed_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_sw_survey_forms_public_key', '{{%sw_survey_forms}}', 'public_key');
        $this->createIndex('idx_sw_survey_questions_survey', '{{%sw_survey_questions}}', 'survey_id');
        $this->createIndex('idx_sw_survey_options_question', '{{%sw_survey_question_options}}', 'question_id');
        $this->createIndex('idx_sw_survey_urls_survey', '{{%sw_survey_urls}}', 'survey_id');
        $this->createIndex('idx_sw_survey_roles_unique', '{{%sw_survey_roles}}', ['survey_id', 'role_id'], true);
        $this->createIndex('idx_sw_survey_responses_unique', '{{%sw_survey_responses}}', ['public_key', 'survey_id', 'visitor_key'], true);

        $this->addForeignKey('fk_sw_survey_questions_survey', '{{%sw_survey_questions}}', 'survey_id', '{{%sw_survey_forms}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_survey_options_question', '{{%sw_survey_question_options}}', 'question_id', '{{%sw_survey_questions}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_survey_urls_survey', '{{%sw_survey_urls}}', 'survey_id', '{{%sw_survey_forms}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_survey_roles_survey', '{{%sw_survey_roles}}', 'survey_id', '{{%sw_survey_forms}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_survey_roles_role', '{{%sw_survey_roles}}', 'role_id', '{{%roles}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_survey_responses_survey', '{{%sw_survey_responses}}', 'survey_id', '{{%sw_survey_forms}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%sw_survey_responses}}');
        $this->dropTable('{{%sw_survey_roles}}');
        $this->dropTable('{{%sw_survey_urls}}');
        $this->dropTable('{{%sw_survey_question_options}}');
        $this->dropTable('{{%sw_survey_questions}}');
        $this->dropTable('{{%sw_survey_forms}}');
    }
}
