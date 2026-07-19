<?php

namespace app\Modules\Support\Application\Dto;

final class SupportConversationListResponse
{
    public function __construct(
        private readonly array $conversations,
        private readonly array $limits = [],
    ) {
    }

    public function toArray(): array
    {
        $response = [
            'conversations' => array_map(
                static fn($conversation) => (new SupportConversationResponse($conversation))->toArray()['conversation'],
                $this->conversations,
            ),
        ];

        if ($this->limits !== []) {
            $response['limits'] = $this->limits;
        }

        return $response;
    }
}
