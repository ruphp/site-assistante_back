<?php

use yii\db\Migration;

final class m260905_000001_add_support_plan_expiration extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn(
            'support_settings',
            'plan_expires_at',
            $this->dateTime()->null()
        );
        $this->addColumn(
            'support_settings',
            'trial_started_at',
            $this->dateTime()->null()
        );
        $this->createIndex(
            'idx_support_settings_plan_expires_at',
            'support_settings',
            'plan_expires_at'
        );
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx_support_settings_plan_expires_at', 'support_settings');
        $this->dropColumn('support_settings', 'trial_started_at');
        $this->dropColumn('support_settings', 'plan_expires_at');
    }
}
