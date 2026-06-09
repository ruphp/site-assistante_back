<?php

namespace app\Modules\Support\Application\Contract;

interface SupportUsageRepositoryInterface
{
    public function monthlyConversationCount(int $publicKey, \DateTimeImmutable $month): int;

    public function monthlyMessageCount(int $publicKey, \DateTimeImmutable $month): int;

    public function incrementConversations(int $publicKey, \DateTimeImmutable $month): void;

    public function incrementMessages(int $publicKey, \DateTimeImmutable $month): void;
}
