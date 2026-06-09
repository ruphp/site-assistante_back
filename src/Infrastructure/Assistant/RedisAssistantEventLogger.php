<?php

namespace app\Infrastructure\Assistant;

use app\Application\Assistant\Contract\AssistantEventLoggerInterface;
use app\Domain\Assistant\AssistantContext;
use Yii;

final class RedisAssistantEventLogger implements AssistantEventLoggerInterface
{
    public function logOpen(AssistantContext $context): int
    {
        return Yii::$app->redis->lpush('log/open/' . $context->client->publicKey, json_encode([
            'date' => date('Y-m-d H:i:s'),
            'userId' => $context->user->studentId,
            'userRoles' => $context->user->systemRoleIds,
        ]));
    }

    public function logUsage(AssistantContext $context, string $type): int
    {
        return Yii::$app->redis->lpush(
            'log/usage/' . $context->client->publicKey,
            json_encode([
                'date' => date('Y-m-d H:i:s'),
                'userId' => $context->user->studentId,
                'userRoles' => $context->user->systemRoleIds,
                'type' => $type,
            ])
        );
    }
}
