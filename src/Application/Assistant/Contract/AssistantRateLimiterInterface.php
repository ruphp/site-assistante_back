<?php

namespace app\Application\Assistant\Contract;

use app\Application\Assistant\Dto\AssistantRequestContext;

interface AssistantRateLimiterInterface
{
    public function hit(int $publicKey, AssistantRequestContext $requestContext, string $action): void;
}
