<?php

use yii\db\Migration;

final class m260715_000002_onboarding_auto_start extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%sw_onboardings}}', 'auto_start', $this->boolean()->notNull()->defaultValue(true)->after('is_blur'));
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%sw_onboardings}}', 'auto_start');
    }
}
