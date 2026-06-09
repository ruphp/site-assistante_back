<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportRealtimeTokenIssuerInterface;
use Yii;

final class RedisSupportRealtimeTokenIssuer implements SupportRealtimeTokenIssuerInterface
{
    public function issueManagerToken(int $publicKey, int $ttlSeconds = 300): string
    {
        $token = bin2hex(random_bytes(32));

        Yii::$app->redis->executeCommand('SETEX', [
            'support:ws:manager:' . $token,
            $ttlSeconds,
            (string)$publicKey,
        ]);

        return $token;
    }
}
