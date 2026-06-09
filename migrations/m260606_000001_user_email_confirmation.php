<?php

use yii\db\Migration;

final class m260606_000001_user_email_confirmation extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('users', 'email_confirm_token', $this->string(128)->null());
        $this->addColumn('users', 'email_confirmed_at', $this->dateTime()->null());
        $this->createIndex('idx_users_email_confirm_token', 'users', 'email_confirm_token', true);
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx_users_email_confirm_token', 'users');
        $this->dropColumn('users', 'email_confirmed_at');
        $this->dropColumn('users', 'email_confirm_token');
    }
}
