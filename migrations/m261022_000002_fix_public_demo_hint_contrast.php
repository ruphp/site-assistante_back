<?php

use yii\db\Migration;

final class m261022_000002_fix_public_demo_hint_contrast extends Migration
{
    public function safeUp(): void
    {
        $this->update('{{%sw_onboarding_hints}}', ['background_color' => '#FFFFFF'], [
            'public_key' => 2,
            'title' => ['Демо: статья из подсказки /onboarding', 'Демо: статья из подсказки /faq-instructions'],
            'theme' => 'light',
            'background_color' => '#2B245C',
        ]);
    }

    public function safeDown(): void
    {
        // Keep the accessible colors when rolling back unrelated code.
    }
}
