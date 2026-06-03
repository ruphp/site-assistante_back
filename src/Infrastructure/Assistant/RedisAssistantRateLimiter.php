<?php

namespace app\Infrastructure\Assistant;

use app\Application\Assistant\Contract\AssistantRateLimiterInterface;
use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Exception\AssistantRateLimitExceededException;
use Yii;

final class RedisAssistantRateLimiter implements AssistantRateLimiterInterface
{
    public function __construct(
        private readonly ?int $clientLimit = null,
        private readonly ?int $visitorLimit = null,
        private readonly ?int $windowSeconds = null,
    ) {
    }

    public function hit(int $publicKey, AssistantRequestContext $requestContext, string $action): void
    {
        $clientLimit = $this->clientLimit ?? (int)($_ENV['ASSISTANT_API_CLIENT_RATE_LIMIT'] ?? 1000);
        $visitorLimit = $this->visitorLimit ?? (int)($_ENV['ASSISTANT_API_VISITOR_RATE_LIMIT'] ?? 120);
        $windowSeconds = $this->windowSeconds ?? (int)($_ENV['ASSISTANT_API_RATE_WINDOW_SECONDS'] ?? 60);

        if ($windowSeconds <= 0) {
            return;
        }

        $this->hitBucket($this->clientKey($publicKey, $action), $clientLimit, $windowSeconds);
        $this->hitBucket($this->visitorKey($publicKey, $requestContext, $action), $visitorLimit, $windowSeconds);
    }

    private function hitBucket(string $key, int $limit, int $windowSeconds): void
    {
        if ($limit <= 0) {
            return;
        }

        $count = (int)Yii::$app->redis->incr($key);

        if ($count === 1) {
            Yii::$app->redis->expire($key, $windowSeconds);
        }

        if ($count > $limit) {
            throw new AssistantRateLimitExceededException('Too many assistant API requests');
        }
    }

    private function clientKey(int $publicKey, string $action): string
    {
        return 'rate/assistant/client/' . $action . '/' . $publicKey;
    }

    private function visitorKey(int $publicKey, AssistantRequestContext $requestContext, string $action): string
    {
        $ip = $requestContext->remoteAddr !== '' ? $requestContext->remoteAddr : 'unknown';

        return 'rate/assistant/visitor/' . $action . '/' . $publicKey . '/' . sha1($ip);
    }
}
