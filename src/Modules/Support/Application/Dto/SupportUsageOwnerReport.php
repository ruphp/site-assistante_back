<?php

namespace app\Modules\Support\Application\Dto;

final class SupportUsageOwnerReport
{
    public function __construct(
        public readonly int $ownerPublicKey,
        public readonly int $ownerUserId,
        public readonly string $ownerName,
        public readonly string $ownerEmail,
        public readonly string $firm,
        public readonly string $plan,
        public readonly string $planLabel,
        public readonly int $operatorRepliesToday,
        public readonly int $operatorRepliesPerDayLimit,
        public readonly int $conversationsMonth,
        public readonly int $conversationsMonthLimit,
        public readonly int $messagesMonth,
        public readonly int $messagesMonthLimit,
        public readonly int $projectsCount,
        public readonly int $projectsLimit,
        public readonly int $operatorsCount,
        public readonly int $operatorsLimit,
        public readonly array $projects,
    ) {
    }
}
