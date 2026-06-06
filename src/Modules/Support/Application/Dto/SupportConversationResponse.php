<?php

namespace app\Modules\Support\Application\Dto;

use app\Modules\Support\Domain\SupportConversation;

final class SupportConversationResponse
{
    public function __construct(
        public readonly SupportConversation $conversation,
    ) {
    }

    public function toArray(): array
    {
        return [
            'conversation' => [
                'id' => $this->conversation->id,
                'public_key' => $this->conversation->publicKey,
                'visitor_id' => $this->conversation->visitorId,
                'visitor_email' => $this->conversation->visitorEmail,
                'page_url' => $this->conversation->pageUrl,
                'status' => $this->conversation->status,
                'entry_point_id' => $this->conversation->entryPointId,
                'priority' => $this->conversation->priority,
                'last_message_at' => $this->conversation->lastMessageAt,
                'last_sender_type' => $this->conversation->lastSenderType,
                'waits_for_operator' => $this->conversation->waitsForOperator(),
                'waiting_seconds' => $this->conversation->waitingSeconds(),
                'waiting_level' => $this->conversation->waitingLevel(),
            ],
        ];
    }
}
