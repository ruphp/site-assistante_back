<?php

use yii\db\Migration;

final class m261022_000003_position_public_demo_hints extends Migration
{
    public function safeUp(): void
    {
        $this->update('{{%sw_onboarding_hints}}', ['is_leftward' => true], [
            'public_key' => 2,
            'title' => ['Демо: статья из подсказки /onboarding', 'Демо: статья из подсказки /faq-instructions'],
        ]);
    }

    public function safeDown(): void
    {
    }
}
