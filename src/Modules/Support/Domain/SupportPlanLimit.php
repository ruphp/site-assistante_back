<?php

namespace app\Modules\Support\Domain;

final class SupportPlanLimit
{
    public function __construct(
        public readonly int $maxOperators,
        public readonly int $maxConversationsPerMonth,
        public readonly int $maxMessagesPerMonth,
        public readonly int $historyDays,
        public readonly bool $attachmentsEnabled,
    ) {
    }

    public static function free(): self
    {
        return new self(
            maxOperators: 1,
            maxConversationsPerMonth: 100,
            maxMessagesPerMonth: 1000,
            historyDays: 30,
            attachmentsEnabled: false,
        );
    }

    public function canStartConversation(int $usedConversations): bool
    {
        return $usedConversations < $this->maxConversationsPerMonth;
    }

    public function canSendMessage(int $usedMessages): bool
    {
        return $usedMessages < $this->maxMessagesPerMonth;
    }
}
