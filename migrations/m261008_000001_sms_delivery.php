<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

final class m261008_000001_sms_delivery extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('sms_delivery_projects', [
            'id' => Schema::TYPE_PK,
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'api_token_hash' => Schema::TYPE_STRING . '(64) NOT NULL',
            'api_token_prefix' => Schema::TYPE_STRING . '(16) NOT NULL',
            'callback_url' => Schema::TYPE_STRING . '(1024) NULL',
            'callback_secret' => Schema::TYPE_STRING . '(128) NOT NULL',
            'enabled' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
        $this->createIndex('uidx_sms_delivery_projects_public_key', 'sms_delivery_projects', 'public_key', true);
        $this->createIndex('uidx_sms_delivery_projects_token_hash', 'sms_delivery_projects', 'api_token_hash', true);

        $this->createTable('sms_delivery_tasks', [
            'id' => Schema::TYPE_BIGPK,
            'public_key' => Schema::TYPE_INTEGER . ' NOT NULL',
            'request_id' => Schema::TYPE_STRING . '(128) NOT NULL',
            'phone' => Schema::TYPE_STRING . '(32) NOT NULL',
            'sms_text' => Schema::TYPE_TEXT . ' NOT NULL',
            'status' => Schema::TYPE_STRING . '(32) NOT NULL',
            'expires_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'opened_at' => Schema::TYPE_DATETIME . ' NULL',
            'opened_by_user_id' => Schema::TYPE_INTEGER . ' NULL',
            'request_ip_hash' => Schema::TYPE_STRING . '(64) NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
        $this->createIndex('uidx_sms_delivery_tasks_request', 'sms_delivery_tasks', ['public_key', 'request_id'], true);
        $this->createIndex('idx_sms_delivery_tasks_queue', 'sms_delivery_tasks', ['public_key', 'status', 'expires_at']);
        $this->createIndex('idx_sms_delivery_tasks_phone_created', 'sms_delivery_tasks', ['public_key', 'phone', 'created_at']);

        $this->createTable('sms_delivery_events', [
            'id' => Schema::TYPE_BIGPK,
            'task_id' => Schema::TYPE_BIGINT . ' NOT NULL',
            'status' => Schema::TYPE_STRING . '(32) NOT NULL',
            'details' => Schema::TYPE_TEXT . ' NULL',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);
        $this->createIndex('idx_sms_delivery_events_task', 'sms_delivery_events', ['task_id', 'id']);
    }

    public function safeDown(): void
    {
        $this->dropTable('sms_delivery_events');
        $this->dropTable('sms_delivery_tasks');
        $this->dropTable('sms_delivery_projects');
    }
}
