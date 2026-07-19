<?php

use yii\db\Migration;

final class m260719_000003_fix_demo_onboarding_selectors extends Migration
{
    private const PUBLIC_KEY = 2;

    public function safeUp(): void
    {
        if ($this->db->getTableSchema('{{%sw_onboardings}}') === null) {
            return;
        }

        $onePageId = $this->onboardingId('Демо одностраничного онбординга');
        if ($onePageId !== null) {
            $sectionId = $this->sectionId($onePageId, 'Главная страница');
            if ($sectionId !== null) {
                $this->updateStepSelector(
                    $sectionId,
                    10,
                    '.sw-header__login, .sw-header__actions a, a[href="/login"], a[href*="/login"], a[href="/manager"], a[href*="/manager"]'
                );
            }
        }

        $multiPageId = $this->onboardingId('Демо многостраничного онбординга');
        if ($multiPageId !== null) {
            $homeSectionId = $this->sectionId($multiPageId, 'Старт на главной');
            if ($homeSectionId !== null) {
                $this->updateStepSelector(
                    $homeSectionId,
                    10,
                    'a[href="/join"], a[href*="/join"], .sw-header__login, .sw-header__actions a, a[href="/login"], a[href*="/login"]'
                );
            }

            $joinSectionId = $this->sectionId($multiPageId, 'Регистрация');
            if ($joinSectionId !== null) {
                $this->updateStepSelector($joinSectionId, 10, '#user-join-form, a[href*="yandex"]');
                $this->updateStepSelector($joinSectionId, 20, '#userjoinform-email, button[type="submit"], input[type="submit"]');
            }
        }
    }

    public function safeDown(): void
    {
    }

    private function onboardingId(string $title): ?int
    {
        $id = (new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboardings}}')
            ->where(['public_key' => self::PUBLIC_KEY, 'title' => $title])
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

    private function updateStepSelector(int $sectionId, int $sortOrder, string $selector): void
    {
        $this->update(
            '{{%sw_onboarding_steps}}',
            ['selector' => $selector],
            ['section_id' => $sectionId, 'sort_order' => $sortOrder]
        );
    }
}
