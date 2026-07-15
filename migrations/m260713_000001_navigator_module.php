<?php

use yii\db\Migration;

final class m260713_000001_navigator_module extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%sw_onboarding_hints}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'content' => $this->text()->notNull()->defaultValue(''),
            'selector' => $this->string(1000)->notNull()->defaultValue(''),
            'position' => $this->integer()->notNull()->defaultValue(2),
            'type' => $this->integer()->notNull()->defaultValue(2),
            'view_type' => $this->string(64)->notNull()->defaultValue('circle'),
            'icon_type' => $this->string(64)->notNull()->defaultValue('question'),
            'icon_color' => $this->string(32)->notNull()->defaultValue('#FBBF24'),
            'background_color' => $this->string(32)->notNull()->defaultValue('#2B245C'),
            'theme' => $this->string(16)->notNull()->defaultValue('light'),
            'trigger_type' => $this->string(16)->notNull()->defaultValue('hover'),
            'hide_after_view' => $this->boolean()->notNull()->defaultValue(false),
            'button_label' => $this->string(80)->notNull()->defaultValue(''),
            'button_url' => $this->string(500)->notNull()->defaultValue(''),
            'button_instruction_id' => $this->integer()->null(),
            'type_bind' => $this->boolean()->notNull()->defaultValue(false),
            'is_leftward' => $this->boolean()->notNull()->defaultValue(false),
            'standalone_enabled' => $this->boolean()->notNull()->defaultValue(false),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'autostart' => $this->integer()->notNull()->defaultValue(0),
            'vision' => $this->integer()->notNull()->defaultValue(0),
            'instruction_id' => $this->integer()->null(),
            'left_offset' => $this->integer()->notNull()->defaultValue(0),
            'top_offset' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createTable('{{%sw_onboarding_hint_roles}}', [
            'id' => $this->primaryKey(),
            'hint_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%sw_onboarding_hint_urls}}', [
            'id' => $this->primaryKey(),
            'hint_id' => $this->integer()->notNull(),
            'public_key' => $this->integer()->notNull(),
            'url' => $this->string(500)->notNull(),
            'include_children' => $this->boolean()->notNull()->defaultValue(false),
            'include_query' => $this->boolean()->notNull()->defaultValue(false),
        ]);

        $this->createTable('{{%sw_onboardings}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'timeout' => $this->integer()->notNull()->defaultValue(0),
            'type' => $this->integer()->notNull()->defaultValue(0),
            'is_blur' => $this->boolean()->notNull()->defaultValue(false),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'sort_order' => $this->integer()->notNull()->defaultValue(100),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createTable('{{%sw_onboarding_roles}}', [
            'id' => $this->primaryKey(),
            'onboarding_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%sw_onboarding_sections}}', [
            'id' => $this->primaryKey(),
            'onboarding_id' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull()->defaultValue('Раздел'),
            'url' => $this->string(500)->notNull()->defaultValue(''),
            'include_children' => $this->boolean()->notNull()->defaultValue(false),
            'include_query' => $this->boolean()->notNull()->defaultValue(false),
            'sort_order' => $this->integer()->notNull()->defaultValue(100),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ]);

        $this->createTable('{{%sw_onboarding_steps}}', [
            'id' => $this->primaryKey(),
            'section_id' => $this->integer()->notNull(),
            'hint_id' => $this->integer()->null(),
            'text' => $this->text()->notNull()->defaultValue(''),
            'selector' => $this->string(1000)->notNull()->defaultValue(''),
            'position' => $this->integer()->notNull()->defaultValue(2),
            'sort_order' => $this->integer()->notNull()->defaultValue(100),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ]);

        $this->createTable('{{%sw_onboarding_progress}}', [
            'id' => $this->primaryKey(),
            'public_key' => $this->integer()->notNull(),
            'onboarding_id' => $this->integer()->notNull(),
            'visitor_key' => $this->string(255)->notNull(),
            'count_viewed' => $this->integer()->notNull()->defaultValue(0),
            'count_unviewed' => $this->integer()->notNull()->defaultValue(0),
            'is_finished' => $this->boolean()->notNull()->defaultValue(false),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_sw_onboarding_hints_public_key', '{{%sw_onboarding_hints}}', 'public_key');
        $this->createIndex('idx_sw_onboarding_hint_roles_unique', '{{%sw_onboarding_hint_roles}}', ['hint_id', 'role_id'], true);
        $this->createIndex('idx_sw_onboarding_hint_urls_hint', '{{%sw_onboarding_hint_urls}}', 'hint_id');
        $this->createIndex('idx_sw_onboardings_public_key', '{{%sw_onboardings}}', 'public_key');
        $this->createIndex('idx_sw_onboarding_roles_unique', '{{%sw_onboarding_roles}}', ['onboarding_id', 'role_id'], true);
        $this->createIndex('idx_sw_onboarding_sections_onboarding', '{{%sw_onboarding_sections}}', 'onboarding_id');
        $this->createIndex('idx_sw_onboarding_steps_section', '{{%sw_onboarding_steps}}', 'section_id');
        $this->createIndex('idx_sw_onboarding_progress_unique', '{{%sw_onboarding_progress}}', ['public_key', 'onboarding_id', 'visitor_key'], true);

        $this->addForeignKey('fk_sw_onboarding_hint_roles_hint', '{{%sw_onboarding_hint_roles}}', 'hint_id', '{{%sw_onboarding_hints}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_onboarding_hint_roles_role', '{{%sw_onboarding_hint_roles}}', 'role_id', '{{%roles}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_onboarding_hint_urls_hint', '{{%sw_onboarding_hint_urls}}', 'hint_id', '{{%sw_onboarding_hints}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_onboarding_roles_onboarding', '{{%sw_onboarding_roles}}', 'onboarding_id', '{{%sw_onboardings}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_onboarding_roles_role', '{{%sw_onboarding_roles}}', 'role_id', '{{%roles}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_onboarding_sections_onboarding', '{{%sw_onboarding_sections}}', 'onboarding_id', '{{%sw_onboardings}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_onboarding_steps_section', '{{%sw_onboarding_steps}}', 'section_id', '{{%sw_onboarding_sections}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_sw_onboarding_steps_hint', '{{%sw_onboarding_steps}}', 'hint_id', '{{%sw_onboarding_hints}}', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%sw_onboarding_progress}}');
        $this->dropTable('{{%sw_onboarding_steps}}');
        $this->dropTable('{{%sw_onboarding_sections}}');
        $this->dropTable('{{%sw_onboarding_roles}}');
        $this->dropTable('{{%sw_onboardings}}');
        $this->dropTable('{{%sw_onboarding_hint_urls}}');
        $this->dropTable('{{%sw_onboarding_hint_roles}}');
        $this->dropTable('{{%sw_onboarding_hints}}');
    }
}
