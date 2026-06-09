<?php

namespace app\Application\Assistant\Contract;

use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Domain\Assistant\AssistantContext;

interface AssistantContextRepositoryInterface
{
    public function getByPublicKey(int $publicKey, ?AssistantRequestContext $requestContext = null): AssistantContext;
}
