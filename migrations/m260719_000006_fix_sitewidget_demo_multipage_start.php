<?php

use yii\db\Migration;

final class m260719_000006_fix_sitewidget_demo_multipage_start extends Migration
{
    public function safeUp(): void
    {
        $onboardingId = (int)$this->db->createCommand(
            'SELECT id FROM {{%sw_onboardings}} WHERE public_key = :publicKey AND title = :title LIMIT 1',
            [':publicKey' => 2, ':title' => 'Демо многостраничного онбординга']
        )->queryScalar();

        if ($onboardingId <= 0) {
            return;
        }

        $sectionId = (int)$this->db->createCommand(
            'SELECT id FROM {{%sw_onboarding_sections}} WHERE onboarding_id = :onboardingId AND title = :title LIMIT 1',
            [':onboardingId' => $onboardingId, ':title' => 'Старт на главной']
        )->queryScalar();

        if ($sectionId > 0) {
            $this->update(
                '{{%sw_onboarding_steps}}',
                [
                    'selector' => '#modules, #how',
                    'text' => 'Здесь собраны основные модули SiteWidget и показан общий путь работы с сайтом.',
                ],
                ['section_id' => $sectionId, 'sort_order' => 20]
            );
        }
    }

    public function safeDown(): void
    {
        $onboardingId = (int)$this->db->createCommand(
            'SELECT id FROM {{%sw_onboardings}} WHERE public_key = :publicKey AND title = :title LIMIT 1',
            [':publicKey' => 2, ':title' => 'Демо многостраничного онбординга']
        )->queryScalar();

        if ($onboardingId <= 0) {
            return;
        }

        $sectionId = (int)$this->db->createCommand(
            'SELECT id FROM {{%sw_onboarding_sections}} WHERE onboarding_id = :onboardingId AND title = :title LIMIT 1',
            [':onboardingId' => $onboardingId, ':title' => 'Старт на главной']
        )->queryScalar();

        if ($sectionId > 0) {
            $this->update(
                '{{%sw_onboarding_steps}}',
                ['selector' => '#pricing'],
                ['section_id' => $sectionId, 'sort_order' => 20]
            );
        }
    }
}
