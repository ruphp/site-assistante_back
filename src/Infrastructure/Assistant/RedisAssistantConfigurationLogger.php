<?php

namespace app\Infrastructure\Assistant;

use app\Application\Assistant\Contract\AssistantConfigurationLoggerInterface;
use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Domain\Assistant\AssistantContext;
use Yii;

final class RedisAssistantConfigurationLogger implements AssistantConfigurationLoggerInterface
{
    public function log(BuildAssistantConfigurationRequest $request, AssistantContext $context): void
    {
        Yii::$app->redis->lpush('log/configuration/' . $request->publicKey, json_encode([
            'date' => date('Y-m-d H:i:s'),
            'userId' => $context->user->studentId,
        ]));
    }
}
