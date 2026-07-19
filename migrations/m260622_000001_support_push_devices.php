<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

final class m260622_000001_support_push_devices extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('support_push_devices', [
            'id' => Schema::TYPE_PK,
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'token' => Schema::TYPE_STRING . '(255) NOT NULL',
            'platform' => Schema::TYPE_STRING . '(32) NOT NULL DEFAULT \'android\'',
            'is_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            'last_seen_at' => Schema::TYPE_DATETIME . ' NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);

        $this->createIndex('idx_support_push_devices_public_key', 'support_push_devices', 'public_key');
        $this->createIndex('idx_support_push_devices_user_id', 'support_push_devices', 'user_id');
        $this->createIndex('uidx_support_push_devices_token', 'support_push_devices', 'token', true);
    }

    public function safeDown(): void
    {
        $this->dropIndex('uidx_support_push_devices_token', 'support_push_devices');
        $this->dropIndex('idx_support_push_devices_user_id', 'support_push_devices');
        $this->dropIndex('idx_support_push_devices_public_key', 'support_push_devices');
        $this->dropTable('support_push_devices');
    }
}
