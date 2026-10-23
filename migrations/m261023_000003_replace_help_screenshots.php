<?php

use yii\db\Migration;
use yii\db\Query;

final class m261023_000003_replace_help_screenshots extends Migration
{
    public function safeUp(): void
    {
        $guides = [
            'Как создать первую подсказку' => 'create-hint',
            'Как создать пошаговый сценарий на одной странице' => 'create-onboarding',
            'Как создать раздел и первую статью' => 'create-instruction',
        ];
        foreach ($guides as $title => $image) {
            $row = (new Query())->from('sw_instruction_articles')->where(['public_key' => 2, 'title' => $title])->one($this->db);
            if (!$row) continue;
            $old = '/img/help/' . $image . '.png';
            if (!str_contains($row['html'], $old)) continue;
            // Replace only our seeded illustration fragment, preserving the article's other edits.
            $html = str_replace(
                [$old, '<h3>Пример заполнения</h3>', 'Это пример настроек, а не готовый объект вашего проекта. Нажмите изображение, чтобы открыть его в полном размере.'],
                ['/img/help/' . $image . '-detail.png', '<h3>Основные поля формы</h3>', 'На снимке - нужный блок настроек без меню и соседних элементов. Названия и значения задайте для своего проекта.'],
                $row['html'],
            );
            $this->update('sw_instruction_articles', ['html' => $html, 'content_bytes' => strlen($html)], ['id' => $row['id'], 'public_key' => 2]);
        }
    }

    public function safeDown(): bool
    {
        return false;
    }
}
