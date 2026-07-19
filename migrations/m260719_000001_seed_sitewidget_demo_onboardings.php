<?php

use yii\db\Migration;

final class m260719_000001_seed_sitewidget_demo_onboardings extends Migration
{
    private const PUBLIC_KEY = 2;
    private const ONE_PAGE_TITLE = 'Демо одностраничного онбординга';
    private const MULTI_PAGE_TITLE = 'Демо многостраничного онбординга';

    public function safeUp(): void
    {
        if ($this->db->getTableSchema('{{%sw_onboardings}}') === null) {
            return;
        }

        $onePageId = $this->ensureOnboarding(self::ONE_PAGE_TITLE, 10);
        $onePageSectionId = $this->ensureSection($onePageId, 'Главная страница', '/', 10);
        $this->ensureStep($onePageSectionId, 'Кнопка входа ведет владельца сайта в личный кабинет SiteWidget.', '.sw-header__login, .sw-header__actions a, a[href="/login"], a[href*="/login"], a[href="/manager"], a[href*="/manager"]', 1, 10);
        $this->ensureStep($onePageSectionId, 'Здесь собраны модули: онлайн-поддержка, инструкции, онбординг и анкеты.', '#modules', 2, 20);
        $this->ensureStep($onePageSectionId, 'В этом блоке показано, как менеджер получает обращения и отвечает посетителям.', '#app, #support, [href*="support"]', 2, 30);
        $this->ensureStep($onePageSectionId, 'Тарифы помогают выбрать лимиты ответов, проекты и набор модулей.', '#pricing, [href*="pricing"]', 2, 40);

        $multiPageId = $this->ensureOnboarding(self::MULTI_PAGE_TITLE, 20);
        $homeSectionId = $this->ensureSection($multiPageId, 'Старт на главной', '/', 10);
        $this->ensureStep($homeSectionId, 'Начните с кнопки регистрации или входа через Яндекс.', 'a[href="/join"], a[href*="/join"], .sw-header__login, .sw-header__actions a, a[href="/login"], a[href*="/login"]', 1, 10);
        $this->ensureStep($homeSectionId, 'После выбора тарифа переходите к созданию проекта и подключению виджета.', '#pricing', 2, 20);

        $joinSectionId = $this->ensureSection($multiPageId, 'Регистрация', '/join', 20);
        $this->ensureStep($joinSectionId, 'На странице регистрации можно создать аккаунт по email или войти через Яндекс.', '#user-join-form, a[href*="yandex"]', 2, 10);
        $this->ensureStep($joinSectionId, 'После регистрации откроется личный кабинет со списком проектов.', '#userjoinform-email, button[type="submit"], input[type="submit"]', 2, 20);
    }

    public function safeDown(): void
    {
        $ids = (new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboardings}}')
            ->where([
                'public_key' => self::PUBLIC_KEY,
                'title' => [self::ONE_PAGE_TITLE, self::MULTI_PAGE_TITLE],
            ])
            ->column($this->db);

        if ($ids === []) {
            return;
        }

        $sectionIds = (new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboarding_sections}}')
            ->where(['onboarding_id' => $ids])
            ->column($this->db);

        if ($sectionIds !== []) {
            $this->delete('{{%sw_onboarding_steps}}', ['section_id' => $sectionIds]);
        }
        $this->delete('{{%sw_onboarding_sections}}', ['onboarding_id' => $ids]);
        $this->delete('{{%sw_onboarding_roles}}', ['onboarding_id' => $ids]);
        $this->delete('{{%sw_onboarding_progress}}', ['onboarding_id' => $ids]);
        $this->delete('{{%sw_onboardings}}', ['id' => $ids]);
    }

    private function ensureOnboarding(string $title, int $sortOrder): int
    {
        $id = (new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboardings}}')
            ->where(['public_key' => self::PUBLIC_KEY, 'title' => $title])
            ->scalar($this->db);

        if ($id !== false) {
            $this->update('{{%sw_onboardings}}', [
                'timeout' => 0,
                'type' => 0,
                'is_blur' => false,
                'auto_start' => false,
                'is_active' => true,
                'sort_order' => $sortOrder,
                'updated_at' => new yii\db\Expression('CURRENT_TIMESTAMP'),
            ], ['id' => (int)$id]);

            return (int)$id;
        }

        $this->insert('{{%sw_onboardings}}', [
            'public_key' => self::PUBLIC_KEY,
            'title' => $title,
            'timeout' => 0,
            'type' => 0,
            'is_blur' => false,
            'auto_start' => false,
            'is_active' => true,
            'sort_order' => $sortOrder,
            'created_at' => new yii\db\Expression('CURRENT_TIMESTAMP'),
            'updated_at' => new yii\db\Expression('CURRENT_TIMESTAMP'),
        ]);

        return (int)(new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboardings}}')
            ->where(['public_key' => self::PUBLIC_KEY, 'title' => $title])
            ->scalar($this->db);
    }

    private function ensureSection(int $onboardingId, string $title, string $url, int $sortOrder): int
    {
        $id = (new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboarding_sections}}')
            ->where(['onboarding_id' => $onboardingId, 'title' => $title])
            ->scalar($this->db);

        $data = [
            'url' => $url,
            'include_children' => false,
            'include_query' => false,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ];

        if ($id !== false) {
            $this->update('{{%sw_onboarding_sections}}', $data, ['id' => (int)$id]);

            return (int)$id;
        }

        $this->insert('{{%sw_onboarding_sections}}', array_merge($data, [
            'onboarding_id' => $onboardingId,
            'title' => $title,
        ]));

        return (int)(new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboarding_sections}}')
            ->where(['onboarding_id' => $onboardingId, 'title' => $title])
            ->scalar($this->db);
    }

    private function ensureStep(int $sectionId, string $text, string $selector, int $position, int $sortOrder): void
    {
        $id = (new yii\db\Query())
            ->select('id')
            ->from('{{%sw_onboarding_steps}}')
            ->where(['section_id' => $sectionId, 'sort_order' => $sortOrder])
            ->scalar($this->db);

        $data = [
            'hint_id' => null,
            'text' => $text,
            'selector' => $selector,
            'position' => $position,
            'is_active' => true,
        ];

        if ($id !== false) {
            $this->update('{{%sw_onboarding_steps}}', $data, ['id' => (int)$id]);

            return;
        }

        $this->insert('{{%sw_onboarding_steps}}', array_merge($data, [
            'section_id' => $sectionId,
            'sort_order' => $sortOrder,
        ]));
    }
}
