<?php

use yii\db\Migration;

final class m261023_000004_make_manager_help_once extends Migration
{
    public function safeUp(): void
    {
        $this->update('sw_onboarding_hints', ['hide_after_view' => true], [
            'and',
            ['public_key' => 2],
            ['like', 'title', 'Помощь в кабинете: %', false],
        ]);
    }

    public function safeDown(): bool
    {
        return false;
    }
}
