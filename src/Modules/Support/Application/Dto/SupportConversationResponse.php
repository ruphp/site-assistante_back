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
                'visitor_name' => $this->conversation->visitorName,
                'visitor_email' => $this->conversation->visitorEmail,
                'visitor_phone' => $this->conversation->visitorPhone,
                'page_url' => $this->conversation->pageUrl,
                'project_name' => $this->conversation->projectName,
                'project_domain' => $this->conversation->projectDomain,
                'status' => $this->conversation->status,
                'created_at' => $this->conversation->createdAt,
                'entry_point_id' => $this->conversation->entryPointId,
                'entry_point_title' => $this->conversation->entryPointTitle,
                'entry_point_response_type' => $this->conversation->entryPointResponseType,
                'priority' => $this->conversation->priority,
                'operator_replied_at' => $this->conversation->operatorRepliedAt,
                'operator_seen_at' => $this->conversation->operatorSeenAt,
                'last_visitor_activity_at' => $this->conversation->lastVisitorActivityAt,
                'last_message_at' => $this->conversation->lastMessageAt,
                'last_sender_type' => $this->conversation->lastSenderType,
                'waits_for_operator' => $this->conversation->waitsForOperator(),
                'waiting_seconds' => $this->conversation->waitingSeconds(),
                'waiting_level' => $this->conversation->waitingLevel(),
            ],
        ];
    }
}
