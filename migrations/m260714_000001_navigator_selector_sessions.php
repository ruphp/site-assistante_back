<?php

use yii\db\Migration;

final class m260714_000001_navigator_selector_sessions extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%sw_onboarding_selector_sessions}}', [
            'id' => $this->primaryKey(),
            'token' => $this->string(64)->notNull()->unique(),
            'owner_public_key' => $this->integer()->notNull(),
            'public_key' => $this->integer()->notNull(),
            'target' => $this->string(32)->notNull(),
            'selector' => $this->text(),
            'element_tag' => $this->string(64),
            'element_text' => $this->string(255),
            'page_url' => $this->text(),
            'created_at' => $this->dateTime()->notNull(),
            'selected_at' => $this->dateTime(),
            'expires_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('idx_sw_onboarding_selector_sessions_owner', '{{%sw_onboarding_selector_sessions}}', ['owner_public_key']);
        $this->createIndex('idx_sw_onboarding_selector_sessions_public_key', '{{%sw_onboarding_selector_sessions}}', ['public_key']);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%sw_onboarding_selector_sessions}}');
    }
}
