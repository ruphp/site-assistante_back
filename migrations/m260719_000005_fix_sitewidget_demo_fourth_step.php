<?php

use yii\db\Migration;

final class m260719_000005_fix_sitewidget_demo_fourth_step extends Migration
{
    public function safeUp(): void
    {
        $onboardingId = (int)$this->db->createCommand(
            'SELECT id FROM {{%sw_onboardings}} WHERE public_key = :publicKey AND title = :title LIMIT 1',
            [':publicKey' => 2, ':title' => 'Демо одностраничного онбординга']
        )->queryScalar();

        if ($onboardingId <= 0) {
            return;
        }

        $sectionId = (int)$this->db->createCommand(
            'SELECT id FROM {{%sw_onboarding_sections}} WHERE onboarding_id = :onboardingId AND title = :title LIMIT 1',
            [':onboardingId' => $onboardingId, ':title' => 'Главная страница']
        )->queryScalar();

        if ($sectionId <= 0) {
            return;
        }

        $this->update(
            '{{%sw_onboarding_steps}}',
            ['selector' => '#integrations, #how, .site-landing__cta'],
            ['section_id' => $sectionId, 'sort_order' => 40]
        );
    }

    public function safeDown(): void
    {
        $onboardingId = (int)$this->db->createCommand(
            'SELECT id FROM {{%sw_onboardings}} WHERE public_key = :publicKey AND title = :title LIMIT 1',
            [':publicKey' => 2, ':title' => 'Демо одностраничного онбординга']
        )->queryScalar();

        if ($onboardingId <= 0) {
            return;
        }

        $sectionId = (int)$this->db->createCommand(
            'SELECT id FROM {{%sw_onboarding_sections}} WHERE onboarding_id = :onboardingId AND title = :title LIMIT 1',
            [':onboardingId' => $onboardingId, ':title' => 'Главная страница']
        )->queryScalar();

        if ($sectionId > 0) {
            $this->update(
                '{{%sw_onboarding_steps}}',
                ['selector' => '#pricing, .site-landing__pricing'],
                ['section_id' => $sectionId, 'sort_order' => 40]
            );
        }
    }
}
