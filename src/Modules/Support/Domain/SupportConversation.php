<?php

namespace app\Modules\Support\Domain;

final class SupportConversation
{
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    public function __construct(
        public readonly ?int $id,
        public readonly int $publicKey,
        public readonly string $visitorId,
        public readonly string $status = self::STATUS_OPEN,
    ) {
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }
}
