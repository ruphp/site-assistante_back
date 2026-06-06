<?php

namespace app\Modules\Support\Application\Contract;

interface SupportRealtimeTokenIssuerInterface
{
    public function issueManagerToken(int $publicKey, int $ttlSeconds = 300): string;
}
