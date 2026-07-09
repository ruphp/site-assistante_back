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
        public readonly int $maxEntryPoints,
        public readonly int $maxEntryPointPriority,
        public readonly int $maxOperatorRepliesPerDay = 30,
        public readonly int $maxProjects = 1,
    ) {
    }

    public static function free(): self
    {
        return new self(
            maxOperators: 1,
            maxConversationsPerMonth: 300,
            maxMessagesPerMonth: 3000,
            historyDays: 30,
            attachmentsEnabled: false,
            maxEntryPoints: 1,
            maxEntryPointPriority: 5,
            maxOperatorRepliesPerDay: 30,
            maxProjects: 1,
        );
    }

    public static function start(): self
    {
        return new self(
            maxOperators: 3,
            maxConversationsPerMonth: 3000,
            maxMessagesPerMonth: 30000,
            historyDays: 90,
            attachmentsEnabled: false,
            maxEntryPoints: 3,
            maxEntryPointPriority: 5,
            maxOperatorRepliesPerDay: 200,
            maxProjects: 1,
        );
    }

    public static function pro(): self
    {
        return new self(
            maxOperators: 10,
            maxConversationsPerMonth: 10000,
            maxMessagesPerMonth: 100000,
            historyDays: 180,
            attachmentsEnabled: false,
            maxEntryPoints: 5,
            maxEntryPointPriority: 5,
            maxOperatorRepliesPerDay: 500,
            maxProjects: 5,
        );
    }

    public static function forPlan(string $plan): self
    {
        return match (SupportPlan::normalize($plan)) {
            SupportPlan::START => self::start(),
            SupportPlan::PRO => self::pro(),
            default => self::free(),
        };
    }

    public function canStartConversation(int $usedConversations): bool
    {
        return $usedConversations < $this->maxConversationsPerMonth;
    }

    public function canSendMessage(int $usedMessages): bool
    {
        return $usedMessages < $this->maxMessagesPerMonth;
    }

    public function canOperatorReply(int $usedReplies): bool
    {
        return $usedReplies < $this->maxOperatorRepliesPerDay;
    }

    public function canAddEntryPoint(int $usedEntryPoints): bool
    {
        return $usedEntryPoints < $this->maxEntryPoints;
    }

    public function entryPointRankLimit(): int
    {
        return max(1, min($this->maxEntryPoints, $this->maxEntryPointPriority));
    }
}
