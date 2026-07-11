<?php

use yii\db\Migration;

final class m260712_000001_add_avatar_to_users extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%users}}', 'avatar_path', $this->string(255)->null());
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%users}}', 'avatar_path');
    }
}
