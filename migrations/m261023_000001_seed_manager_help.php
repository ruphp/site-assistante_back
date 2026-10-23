<?php

use yii\db\Migration;
use yii\db\Query;

final class m261023_000001_seed_manager_help extends Migration
{
    private const KEY = 2;

    public function safeUp(): void
    {
        $groups = require __DIR__ . '/data/manager-help-20261023.php';
        foreach (array_keys($groups) as $groupIndex => $name) {
            $category = $this->ensure('sw_instruction_categories', ['public_key' => self::KEY, 'name' => $name], [
                'is_active' => true, 'sort_order' => 20 + $groupIndex * 10,
            ]);
            foreach ($groups[$name] as $index => $entry) {
                [$title, $html] = $entry;
                if ($index === 0) {
                    $image = ['create-hint', 'create-onboarding', 'create-instruction'][$groupIndex];
                    $html .= '<h3>Пример заполнения</h3><p>Это пример настроек, а не готовый объект вашего проекта. Нажмите изображение, чтобы открыть его в полном размере.</p>'
                        . '<a href="/img/help/' . $image . '.png" target="_blank" rel="noopener noreferrer">'
                        . '<img src="/img/help/' . $image . '.png" alt="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' - пример формы" loading="lazy" style="max-width:100%;height:auto"></a>';
                }
                $article = $this->ensure('sw_instruction_articles', ['public_key' => self::KEY, 'title' => $title], [
                    'category_id' => $category, 'html' => $html, 'content_bytes' => strlen($html),
                    'is_active' => true, 'sort_order' => ($index + 1) * 10,
                ]);
                if ($index === 0) {
                    $entry[2] = ['#help-hints-list, #help-hint-form', '#help-onboarding-list', '#help-instructions-list, #help-instructions-form'][$groupIndex];
                    $entry[3] = ['Начните с пошаговой инструкции: выбор элемента, текст, публикация и проверка на своём сайте.', 'Сначала создайте сценарий, затем раздел страницы и шаги. В статье - весь порядок действий.', 'Создайте раздел, добавьте статью и проверьте её в виджете. Статью можно связать с кнопкой подсказки.'][$groupIndex];
                }
                if (!isset($entry[2], $entry[3])) continue;
                $selector = $entry[2];
                $pages = $index === 0 ? [
                    ['/manager/onboarding/hints', '/manager/onboarding/hint-create', '/manager/onboarding/hint-update'],
                    ['/manager/onboarding'],
                    ['/manager/instructions', '/manager/instructions/create', '/manager/instructions/update'],
                ][$groupIndex] : ($groupIndex === 0
                    ? ['/manager/onboarding/hint-create', '/manager/onboarding/hint-update']
                    : match ($selector) {
                        'label[for="section-url"]' => ['/manager/onboarding/sections'],
                        'label[for="step-hint"]' => ['/manager/onboarding/steps'],
                        default => ['/manager/onboarding/create', '/manager/onboarding/update'],
                    });
                $hint = $this->ensure('sw_onboarding_hints', ['public_key' => self::KEY, 'title' => 'Помощь в кабинете: ' . $title], [
                    'selector' => $selector, 'content' => $entry[3], 'type' => 2, 'is_active' => true,
                    'standalone_enabled' => true, 'trigger_type' => 'click', 'theme' => 'light',
                    'background_color' => '#FFFFFF', 'hide_after_view' => false, 'is_leftward' => false,
                    'button_label' => 'Подробнее', 'button_instruction_id' => $article, 'button_url' => '',
                ]);
                foreach ($pages as $page) {
                    $this->ensure('sw_onboarding_hint_urls', ['hint_id' => $hint, 'url' => $page], [
                        'public_key' => self::KEY, 'include_children' => false, 'include_query' => false,
                    ]);
                }
            }
        }
    }

    public function safeDown(): bool
    {
        echo "Help content is editable; automatic removal is disabled.\n";
        return false;
    }

    private function ensure(string $table, array $identity, array $data): int
    {
        $id = (new Query())->select('id')->from($table)->where($identity)->scalar($this->db);
        if ($id !== false) return (int)$id;
        $this->insert($table, $identity + $data);
        return (int)$this->db->getLastInsertID();
    }
}
