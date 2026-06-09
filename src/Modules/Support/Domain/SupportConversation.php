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
        public readonly ?string $visitorEmail = null,
        public readonly ?string $pageUrl = null,
        public readonly string $status = self::STATUS_OPEN,
        public readonly ?string $lastMessageAt = null,
        public readonly ?string $lastSenderType = null,
        public readonly ?int $entryPointId = null,
        public readonly int $priority = 0,
    ) {
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function waitsForOperator(): bool
    {
        return $this->isOpen() && $this->lastSenderType === SupportMessage::SENDER_VISITOR;
    }

    public function waitingSeconds(): int
    {
        if (!$this->waitsForOperator() || $this->lastMessageAt === null) {
            return 0;
        }

        return max(0, time() - strtotime($this->lastMessageAt));
    }

    public function waitingLevel(): string
    {
        $seconds = $this->waitingSeconds();

        if ($seconds >= 60) {
            return 'red';
        }

        if ($seconds >= 30) {
            return 'yellow';
        }

        return $this->waitsForOperator() ? 'green' : 'none';
    }
}
