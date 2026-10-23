<?php

use yii\db\Migration;
use yii\db\Query;

final class m261023_000002_extend_manager_field_help extends Migration
{
    public function safeUp(): void
    {
        foreach (['label[for="hint-left"]' => 'label[for="hint-top"]', 'label[for="hint-button-instruction"]' => 'label[for="hint-button-url"]'] as $source => $target) {
            $hint = (new Query())->from('sw_onboarding_hints')->where(['public_key' => 2, 'selector' => $source])
                ->andWhere(['like', 'title', 'Помощь в кабинете:'])->one($this->db);
            if (!$hint) continue;
            $title = $hint['title'] . ' (' . ($target === 'label[for="hint-top"]' ? 'Y' : 'ссылка') . ')';
            if ((new Query())->from('sw_onboarding_hints')->where(['public_key' => 2, 'title' => $title])->exists($this->db)) continue;
            $sourceId = $hint['id'];
            unset($hint['id']);
            $hint['title'] = $title;
            $hint['selector'] = $target;
            $this->insert('sw_onboarding_hints', $hint);
            $id = (int)$this->db->getLastInsertID();
            foreach ((new Query())->from('sw_onboarding_hint_urls')->where(['hint_id' => $sourceId])->all($this->db) as $url) {
                unset($url['id']);
                $url['hint_id'] = $id;
                $this->insert('sw_onboarding_hint_urls', $url);
            }
        }
    }

    public function safeDown(): bool
    {
        return false;
    }
}
