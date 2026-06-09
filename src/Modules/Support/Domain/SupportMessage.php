<?php

namespace app\Modules\Support\Domain;

final class SupportMessage
{
    public const SENDER_VISITOR = 'visitor';
    public const SENDER_OPERATOR = 'operator';
    public const SENDER_SYSTEM = 'system';

    public function __construct(
        public readonly ?int $id,
        public readonly int $conversationId,
        public readonly int $publicKey,
        public readonly string $senderType,
        public readonly ?string $senderId,
        public readonly string $body,
        public readonly ?string $createdAt = null,
    ) {
    }
}
