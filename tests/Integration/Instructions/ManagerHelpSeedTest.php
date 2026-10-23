<?php

namespace tests\Integration\Instructions;

use tests\Integration\Support\YiiIntegrationTestCase;
use Yii;
use yii\db\Query;

final class ManagerHelpSeedTest extends YiiIntegrationTestCase
{
    public function testHelpSeedIsRepeatSafeAndLinksHintsToPublishedArticles(): void
    {
        if (!(new Query())->from('users')->where(['public_key' => 2])->exists()) {
            $this->createClient(2);
        }
        require_once dirname(__DIR__, 3) . '/migrations/m261023_000001_seed_manager_help.php';
        require_once dirname(__DIR__, 3) . '/migrations/m261023_000002_extend_manager_field_help.php';
        $migration = new \m261023_000001_seed_manager_help(['db' => Yii::$app->db]);
        $extra = new \m261023_000002_extend_manager_field_help(['db' => Yii::$app->db]);
        $tables = ['sw_instruction_categories', 'sw_instruction_articles', 'sw_onboarding_hints', 'sw_onboarding_hint_urls'];
        $counts = static fn(): array => array_map(static fn(string $table): int => (int)(new Query())->from($table)->count(), $tables);
        ob_start();
        try {
            $migration->safeUp();
            $extra->safeUp();
            $first = $counts();
            $migration->safeUp();
            $extra->safeUp();
            self::assertSame($first, $counts());
        } finally {
            ob_end_clean();
        }
        $groups = require dirname(__DIR__, 3) . '/migrations/data/manager-help-20261023.php';
        foreach ($groups as $entries) {
            foreach ($entries as $entry) {
                self::assertTrue((new Query())->from('sw_instruction_articles')->where([
                    'public_key' => 2, 'title' => $entry[0], 'is_active' => true,
                ])->exists());
            }
        }
        $hints = (new Query())->from('sw_onboarding_hints')->where(['public_key' => 2])
            ->andWhere(['like', 'title', 'Помощь в кабинете:'])->all();
        self::assertCount(25, $hints);
        foreach ($hints as $hint) {
            self::assertLessThanOrEqual(300, mb_strlen($hint['content']));
            self::assertTrue((new Query())->from('sw_instruction_articles')->where([
                'id' => $hint['button_instruction_id'], 'public_key' => 2,
            ])->exists());
            self::assertTrue((new Query())->from('sw_onboarding_hint_urls')->where(['hint_id' => $hint['id']])->exists());
        }
    }
}
