<?php

use yii\db\Migration;

final class m260621_000005_support_conversation_activity_tracking extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%support_conversations}}', 'operator_replied_at', $this->dateTime()->null()->after('status'));
        $this->addColumn('{{%support_conversations}}', 'operator_seen_at', $this->dateTime()->null()->after('operator_replied_at'));
        $this->addColumn('{{%support_conversations}}', 'last_visitor_activity_at', $this->dateTime()->null()->after('operator_seen_at'));

        $this->createIndex(
            'idx-support_conversations-operator_seen_at',
            '{{%support_conversations}}',
            ['status', 'operator_seen_at']
        );
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-support_conversations-operator_seen_at', '{{%support_conversations}}');
        $this->dropColumn('{{%support_conversations}}', 'last_visitor_activity_at');
        $this->dropColumn('{{%support_conversations}}', 'operator_seen_at');
        $this->dropColumn('{{%support_conversations}}', 'operator_replied_at');
    }
}
