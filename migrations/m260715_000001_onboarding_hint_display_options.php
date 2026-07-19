<?php

use yii\db\Migration;

final class m260715_000001_onboarding_hint_display_options extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%sw_onboarding_hints}}', 'theme', $this->string(16)->notNull()->defaultValue('light'));
        $this->addColumn('{{%sw_onboarding_hints}}', 'trigger_type', $this->string(16)->notNull()->defaultValue('hover'));
        $this->addColumn('{{%sw_onboarding_hints}}', 'hide_after_view', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%sw_onboarding_hints}}', 'button_label', $this->string(80)->notNull()->defaultValue(''));
        $this->addColumn('{{%sw_onboarding_hints}}', 'button_url', $this->string(500)->notNull()->defaultValue(''));
        $this->addColumn('{{%sw_onboarding_hints}}', 'button_instruction_id', $this->integer()->null());
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%sw_onboarding_hints}}', 'button_instruction_id');
        $this->dropColumn('{{%sw_onboarding_hints}}', 'button_url');
        $this->dropColumn('{{%sw_onboarding_hints}}', 'button_label');
        $this->dropColumn('{{%sw_onboarding_hints}}', 'hide_after_view');
        $this->dropColumn('{{%sw_onboarding_hints}}', 'trigger_type');
        $this->dropColumn('{{%sw_onboarding_hints}}', 'theme');
    }
}
