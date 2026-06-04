<?php

namespace app\Modules\Support\Application\Dto;

final class SupportMessageListResponse
{
    public function __construct(
        private readonly array $messages,
    ) {
    }

    public function toArray(): array
    {
        return [
            'messages' => array_map(
                static fn($message) => (new SupportMessageResponse($message))->toArray()['message'],
                $this->messages,
            ),
        ];
    }
}
