<?php

namespace app\Application\Assistant;

use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Exception\AssistantAccessDeniedException;
use app\Domain\Assistant\AssistantContext;

final class AssistantAccessGuard
{
    public function assertAllowed(AssistantContext $context, ?AssistantRequestContext $requestContext): void
    {
        if ($requestContext === null || $requestContext->originHost === '') {
            return;
        }

        if ($context->client->allowedHosts() === []) {
            return;
        }

        if (!$context->client->allowsHost($requestContext->originHost)) {
            throw new AssistantAccessDeniedException('Assistant origin is not allowed');
        }
    }
}
