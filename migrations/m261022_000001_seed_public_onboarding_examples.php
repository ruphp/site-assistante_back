<?php

use yii\db\Migration;
use yii\db\Query;

final class m261022_000001_seed_public_onboarding_examples extends Migration
{
    private const KEY = 2;

    public function safeUp(): void
    {
        $category = $this->ensure('sw_instruction_categories', ['public_key' => self::KEY, 'name' => 'Попробуйте SiteWidget'], ['is_active' => true, 'sort_order' => 5]);
        $articles = [
            ['Подсказка рядом с кнопкой: как это работает', '<h2>Помощь в нужный момент</h2><p>Вы открыли эту статью из подсказки на странице. Посетителю не пришлось искать раздел помощи или уходить с сайта.</p><h3>Как повторить на своём сайте</h3><ol><li>Создайте статью в разделе «Инструкции».</li><li>Добавьте подсказку и выберите элемент страницы.</li><li>В кнопке подсказки выберите нужную инструкцию.</li></ol><p>Подсказка может работать отдельно от онбординга. На компьютере и телефоне её можно открыть нажатием на значок вопроса.</p>'],
            ['Онбординг с переходом на другую страницу', '<h2>Один маршрут, несколько страниц</h2><p>Сценарий может начаться на одной странице и продолжиться на другой. Например: выбрать товар, перейти в корзину и оформить заказ.</p><h3>Как собрать маршрут</h3><ol><li>Создайте сценарий в разделе «Онбординг».</li><li>Добавьте раздел для первой страницы и шаги к её элементам.</li><li>Добавьте следующий раздел с адресом другой страницы.</li><li>Укажите шаги на второй странице и проверьте весь маршрут.</li></ol><p>В нашем примере кнопка «Далее» переводит со страницы онбординга на страницу инструкций. SiteWidget продолжает тот же сценарий. Завершение и повторный запуск доступны посетителю.</p>'],
        ];
        $articleIds = [];
        foreach ($articles as [$title, $html]) {
            $articleIds[] = $this->ensure('sw_instruction_articles', ['public_key' => self::KEY, 'title' => $title], [
                'category_id' => $category, 'html' => $html, 'content_bytes' => strlen($html), 'sort_order' => 5, 'is_active' => true,
            ]);
        }

        foreach (['/onboarding', '/faq-instructions'] as $index => $url) {
            $hintId = $this->ensure('sw_onboarding_hints', ['public_key' => self::KEY, 'title' => 'Демо: статья из подсказки ' . $url], [
                'content' => $index === 0 ? 'Это самостоятельная подсказка. Короткого объяснения не хватило? Откройте статью прямо в виджете.' : 'Вы уже на другой странице. Из этой подсказки можно открыть статью о многостраничном сценарии.',
                'selector' => '#sw-demo-article', 'type' => 2, 'standalone_enabled' => true, 'is_active' => true, 'is_leftward' => true,
                'trigger_type' => 'click', 'theme' => 'light', 'background_color' => '#FFFFFF', 'hide_after_view' => false,
                'button_label' => 'Открыть статью', 'button_instruction_id' => $articleIds[$index], 'button_url' => '',
            ]);
            $this->ensure('sw_onboarding_hint_urls', ['hint_id' => $hintId, 'url' => $url], ['public_key' => self::KEY, 'include_children' => false, 'include_query' => false]);
        }

        $one = $this->scenario('Подсказки на одной странице - пример SiteWidget', 5);
        $section = $this->section($one, '/onboarding', 10);
        $this->step($section, '#sw-demo-title', 'Это живой пример. Подсказки привязаны к элементам страницы, а не нарисованы на картинке.', 10);
        $this->step($section, '#sw-demo-hint-target', 'Подсказка объясняет конкретный элемент. Здесь можно рассказать, что делает кнопка или какое значение ввести.', 20);
        $this->step($section, '#sw-demo-route', 'Маршрут не обязан заканчиваться на этой странице. Отдельный пример проведёт вас на страницу инструкций.', 30);
        $this->step($section, '#sw-demo-article', 'После завершения нажмите значок вопроса рядом с этим текстом. Кнопка в подсказке откроет подробную статью в виджете.', 40);

        $multi = $this->scenario('Переход между страницами - пример SiteWidget', 6);
        $start = $this->section($multi, '/onboarding', 10);
        $this->step($start, '#sw-demo-title', 'Начинаем маршрут на странице онбординга. Сценарий продолжится на другой странице этого сайта.', 10);
        $this->step($start, '#sw-demo-route', 'Нажмите «Далее». SiteWidget откроет страницу инструкций и продолжит этот же сценарий.', 20);
        $finish = $this->section($multi, '/faq-instructions', 20);
        $this->step($finish, '#sw-demo-arrival', 'Переход выполнен! Вы на странице инструкций, но проходите тот же маршрут. Так можно провести посетителя из каталога в корзину.', 10);
        $this->step($finish, '#sw-demo-article', 'Завершите маршрут, затем нажмите значок вопроса рядом с этим текстом. В подсказке есть статья о настройке переходов между страницами.', 20);
    }

    public function safeDown(): bool
    {
        echo "Public demo content may have been edited; automatic removal is disabled.\n";
        return false;
    }

    private function ensure(string $table, array $identity, array $data): int
    {
        $id = (new Query())->select('id')->from('{{%' . $table . '}}')->where($identity)->scalar($this->db);
        if ($id !== false) {
            return (int)$id;
        }
        $this->insert('{{%' . $table . '}}', $identity + $data);
        return (int)$this->db->getLastInsertID();
    }

    private function scenario(string $title, int $order): int
    {
        return $this->ensure('sw_onboardings', ['public_key' => self::KEY, 'title' => $title], [
            'timeout' => 0, 'type' => 0, 'auto_start' => false, 'is_blur' => true, 'is_active' => true, 'sort_order' => $order,
        ]);
    }

    private function section(int $scenario, string $url, int $order): int
    {
        return $this->ensure('sw_onboarding_sections', ['onboarding_id' => $scenario, 'url' => $url], [
            'title' => $url === '/onboarding' ? 'Начало: онбординг' : 'Продолжение: инструкции',
            'include_children' => false, 'include_query' => false, 'is_active' => true, 'sort_order' => $order,
        ]);
    }

    private function step(int $section, string $selector, string $text, int $order): void
    {
        $this->ensure('sw_onboarding_steps', ['section_id' => $section, 'sort_order' => $order], [
            'selector' => $selector, 'text' => $text, 'position' => 2, 'is_active' => true,
        ]);
    }
}
