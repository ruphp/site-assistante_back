<?php

namespace app\Modules\Support\Application\Contract;

interface SupportUsageRepositoryInterface
{
    public function monthlyConversationCount(int $publicKey, \DateTimeImmutable $month): int;

    public function monthlyMessageCount(int $publicKey, \DateTimeImmutable $month): int;

    public function dailyOperatorReplyCount(int $publicKey, \DateTimeImmutable $day): int;

    public function incrementConversations(int $publicKey, \DateTimeImmutable $month): void;

    public function incrementMessages(int $publicKey, \DateTimeImmutable $month): void;

    public function incrementOperatorReplies(int $publicKey, \DateTimeImmutable $day): void;

    public function resetOperatorReplies(int $publicKey, \DateTimeImmutable $day): void;
}
