<?php

use yii\db\Migration;

final class m260621_000001_user_mobile_auth_token extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('users', 'mobile_auth_token', $this->string(128)->null());
        $this->createIndex(
            'idx_users_mobile_auth_token',
            'users',
            'mobile_auth_token',
            true
        );
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx_users_mobile_auth_token', 'users');
        $this->dropColumn('users', 'mobile_auth_token');
    }
}
