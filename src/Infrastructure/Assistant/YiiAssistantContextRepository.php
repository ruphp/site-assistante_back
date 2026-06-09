<?php

namespace app\Infrastructure\Assistant;

use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Exception\AssistantContextNotFoundException;
use app\Domain\Assistant\AssistantClient;
use app\Domain\Assistant\AssistantContext;
use app\Domain\Assistant\AssistantUserContext;
use app\Infrastructure\Assistant\Assistant;

final class YiiAssistantContextRepository implements AssistantContextRepositoryInterface
{
    public function getByPublicKey(int $publicKey, ?AssistantRequestContext $requestContext = null): AssistantContext
    {
        $assistant = new Assistant($publicKey, $requestContext);

        if ($assistant->params === []) {
            throw new AssistantContextNotFoundException('Assistant configuration not found');
        }

        return new AssistantContext(
            new AssistantClient($assistant->public_key, $assistant->params),
            new AssistantUserContext($assistant->id_student, $assistant->id_roles_system, $assistant->id_roles),
        );
    }
}
