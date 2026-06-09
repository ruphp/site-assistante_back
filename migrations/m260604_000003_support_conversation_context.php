<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260604_000003_support_conversation_context extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('support_conversations', 'visitor_email', Schema::TYPE_STRING . ' DEFAULT NULL');
        $this->addColumn('support_conversations', 'page_url', $this->string(2048)->defaultValue(null));
        $this->createIndex('idx_support_conversations_client_email', 'support_conversations', ['public_key', 'visitor_email']);
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx_support_conversations_client_email', 'support_conversations');
        $this->dropColumn('support_conversations', 'page_url');
        $this->dropColumn('support_conversations', 'visitor_email');
    }
}
