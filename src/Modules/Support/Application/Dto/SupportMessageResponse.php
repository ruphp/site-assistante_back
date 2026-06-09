<?php

namespace app\Modules\Support\Application\Dto;

use app\Modules\Support\Domain\SupportMessage;

final class SupportMessageResponse
{
    public function __construct(
        public readonly SupportMessage $message,
    ) {
    }

    public function toArray(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversationId,
                'public_key' => $this->message->publicKey,
                'sender_type' => $this->message->senderType,
                'sender_id' => $this->message->senderId,
                'body' => $this->message->body,
                'created_at' => $this->message->createdAt,
            ],
        ];
    }
}
