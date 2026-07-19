<?php

use yii\db\Migration;
use yii\db\pgsql\Schema;

class m260621_000002_support_conversation_visitor_name extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('support_conversations', 'visitor_name', Schema::TYPE_STRING . ' DEFAULT NULL');
    }

    public function safeDown(): void
    {
        $this->dropColumn('support_conversations', 'visitor_name');
    }
}
