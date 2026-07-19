<?php

use yii\db\Migration;

final class m260716_000001_user_operator_display_name extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('users', 'operator_display_name', $this->string(80)->null());
    }

    public function safeDown(): void
    {
        $this->dropColumn('users', 'operator_display_name');
    }
}
