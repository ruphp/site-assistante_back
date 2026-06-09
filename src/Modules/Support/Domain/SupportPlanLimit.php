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
            maxEntryPoints: 1,
            maxEntryPointPriority: 5,
        );
    }

    public static function pro(): self
    {
        return new self(
            maxOperators: 5,
            maxConversationsPerMonth: 1000,
            maxMessagesPerMonth: 10000,
            historyDays: 90,
            attachmentsEnabled: false,
            maxEntryPoints: 5,
            maxEntryPointPriority: 5,
        );
    }

    public static function forPlan(string $plan): self
    {
        return SupportPlan::normalize($plan) === SupportPlan::PRO ? self::pro() : self::free();
    }

    public function canStartConversation(int $usedConversations): bool
    {
        return $usedConversations < $this->maxConversationsPerMonth;
    }

    public function canSendMessage(int $usedMessages): bool
    {
        return $usedMessages < $this->maxMessagesPerMonth;
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
