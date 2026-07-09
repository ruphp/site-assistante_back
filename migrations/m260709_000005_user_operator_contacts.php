<?php

use yii\db\Migration;

final class m260709_000005_user_operator_contacts extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('users', 'phone', $this->string(64)->null());
        $this->addColumn('users', 'telegram', $this->string(128)->null());
        $this->addColumn('users', 'max_contact', $this->string(128)->null());
    }

    public function safeDown(): void
    {
        $this->dropColumn('users', 'max_contact');
        $this->dropColumn('users', 'telegram');
        $this->dropColumn('users', 'phone');
    }
}
