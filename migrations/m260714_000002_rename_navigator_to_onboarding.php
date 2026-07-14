<?php

use yii\db\Migration;

final class m260714_000002_rename_navigator_to_onboarding extends Migration
{
    private array $tables = [
        'sw_navigator_hints' => 'sw_onboarding_hints',
        'sw_navigator_hint_roles' => 'sw_onboarding_hint_roles',
        'sw_navigator_hint_urls' => 'sw_onboarding_hint_urls',
        'sw_navigator_onboardings' => 'sw_onboardings',
        'sw_navigator_onboarding_roles' => 'sw_onboarding_roles',
        'sw_navigator_onboarding_sections' => 'sw_onboarding_sections',
        'sw_navigator_onboarding_steps' => 'sw_onboarding_steps',
        'sw_navigator_onboarding_progress' => 'sw_onboarding_progress',
        'sw_navigator_selector_sessions' => 'sw_onboarding_selector_sessions',
    ];

    public function safeUp(): void
    {
        foreach ($this->tables as $old => $new) {
            if ($this->db->schema->getTableSchema($old, true) !== null
                && $this->db->schema->getTableSchema($new, true) === null) {
                $this->renameTable($old, $new);
            }
        }
    }

    public function safeDown(): void
    {
        foreach (array_reverse($this->tables) as $old => $new) {
            if ($this->db->schema->getTableSchema($new, true) !== null
                && $this->db->schema->getTableSchema($old, true) === null) {
                $this->renameTable($new, $old);
            }
        }
    }
}
