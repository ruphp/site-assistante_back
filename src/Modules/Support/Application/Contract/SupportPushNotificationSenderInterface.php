<?php

namespace app\Modules\Support\Application\Contract;

interface SupportPushNotificationSenderInterface
{
    public function isConfigured(): bool;

    /**
     * @param array<string, scalar|null> $data
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): void;
}
