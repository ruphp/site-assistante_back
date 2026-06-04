<?php

namespace app\Modules\Support\Application\Dto;

final class SupportConversationListResponse
{
    public function __construct(
        private readonly array $conversations,
    ) {
    }

    public function toArray(): array
    {
        return [
            'conversations' => array_map(
                static fn($conversation) => (new SupportConversationResponse($conversation))->toArray()['conversation'],
                $this->conversations,
            ),
        ];
    }
}
