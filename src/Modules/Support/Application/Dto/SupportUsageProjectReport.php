<?php

namespace app\Modules\Support\Application\Dto;

final class SupportUsageProjectReport
{
    public function __construct(
        public readonly int $projectId,
        public readonly string $projectName,
        public readonly string $domain,
        public readonly int $publicKey,
        public readonly string $plan,
        public readonly string $planLabel,
        public readonly int $operatorRepliesToday,
        public readonly int $operatorRepliesPerDayLimit,
        public readonly int $conversationsMonth,
        public readonly int $conversationsMonthLimit,
        public readonly int $messagesMonth,
        public readonly int $messagesMonthLimit,
        public readonly int $instructionStorageBytes,
        public readonly int $instructionStorageLimitBytes,
    ) {
    }
}
