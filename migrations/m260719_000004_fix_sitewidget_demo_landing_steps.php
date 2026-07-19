<?php

use yii\db\Migration;

final class m260719_000004_fix_sitewidget_demo_landing_steps extends Migration
{
    private const PUBLIC_KEY = 2;
    private const ONE_PAGE_TITLE = 'Демо одностраничного онбординга';

    public function safeUp(): void
    {
        if ($this->db->getTableSchema('{{%sw_onboardings}}') === null) {
            return;
        }

        $onboardingId = $this->onboardingId();
        if ($onboardingId === null) {
            return;
        }

        $sectionId = $this->sectionId($onboardingId, 'Главная страница');
        if ($sectionId === null) {
            return;
        }

        $this->updateStep(
            $sectionId,
            30,
            'В этом блоке показано, как онлайн-поддержка принимает обращения и передаёт их менеджерам.',
            '#modules .site-landing__card:nth-child(1), #modules article:nth-child(1)'
        );
        $this->updateStep(
            $sectionId,
            40,
            'В тарифах выбираются лимиты ответов, проекты и набор модулей.',
            '#pricing, .site-landing__pricing'
        );
    }

    public function safeDown(): void
    {
    }

    private function onboardingId(): ?int
    {
        $id = (new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboardings}}')
            ->where(['public_key' => self::PUBLIC_KEY, 'title' => self::ONE_PAGE_TITLE])
            ->scalar($this->db);

        return $id === false ? null : (int)$id;
    }

    private function sectionId(int $onboardingId, string $title): ?int
    {
        $id = (new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboarding_sections}}')
            ->where(['onboarding_id' => $onboardingId, 'title' => $title])
            ->scalar($this->db);

        return $id === false ? null : (int)$id;
    }

    private function updateStep(int $sectionId, int $sortOrder, string $text, string $selector): void
    {
        $this->update(
            '{{%sw_onboarding_steps}}',
            [
                'text' => $text,
                'selector' => $selector,
                'position' => 2,
            ],
            ['section_id' => $sectionId, 'sort_order' => $sortOrder]
        );
    }
}
