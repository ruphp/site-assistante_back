<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportRealtimePublisherInterface;
use app\Modules\Support\Application\Dto\SupportMessageResponse;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;
use Yii;

final class RedisSupportRealtimePublisher implements SupportRealtimePublisherInterface
{
    private const CHANNEL = 'support:events';

    public function publishMessage(SupportConversation $conversation, SupportMessage $message): void
    {
        try {
            Yii::$app->redis->executeCommand('PUBLISH', [
                self::CHANNEL,
                json_encode([
                    'type' => 'support.message',
                    'publicKey' => $conversation->publicKey,
                    'conversationId' => $conversation->id,
                    'visitorId' => $conversation->visitorId,
                    'message' => (new SupportMessageResponse($message))->toArray()['message'],
                ], JSON_THROW_ON_ERROR),
            ]);
        } catch (\Throwable) {
        }
    }
}
