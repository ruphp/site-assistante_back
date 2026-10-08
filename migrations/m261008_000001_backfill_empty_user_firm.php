<?php

use yii\db\Migration;

final class m261008_000001_backfill_empty_user_firm extends Migration
{
    public function safeUp(): void
    {
        $this->execute("UPDATE {{%users}} SET firm = name WHERE BTRIM(COALESCE(firm, '')) = ''");
    }

    public function safeDown(): void
    {
    }
}
