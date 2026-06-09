<?php

namespace app\Modules\Support\Application\UseCase;

use app\Application\Assistant\AssistantAccessGuard;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Exception\AssistantAccessDeniedException;
use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Application\Exception\SupportAccessDeniedException;
use app\Modules\Support\Domain\SupportModule;

class SupportAccessGuard
{
    public function __construct(
        private readonly AssistantContextRepositoryInterface $assistantContextRepository,
        private readonly ClientModuleAccessRepositoryInterface $moduleAccessRepository,
        private readonly SupportSettingsRepositoryInterface $settingsRepository,
        private readonly AssistantAccessGuard $assistantAccessGuard = new AssistantAccessGuard(),
    ) {
    }

    public function assertAvailable(int $publicKey, SupportVisitorContext $context): void
    {
        try {
            $assistantContext = $this->assistantContextRepository->getByPublicKey(
                $publicKey,
                new AssistantRequestContext(
                    pathname: $context->pathname,
                    userId: $context->visitorId,
                    remoteAddr: $context->remoteAddr,
                    originHost: $context->originHost,
                ),
            );
            $this->assistantAccessGuard->assertAllowed($assistantContext, new AssistantRequestContext(
                pathname: $context->pathname,
                userId: $context->visitorId,
                remoteAddr: $context->remoteAddr,
                originHost: $context->originHost,
            ));
        } catch (AssistantAccessDeniedException $e) {
            throw new SupportAccessDeniedException($e->getMessage(), previous: $e);
        }

        if (!$this->moduleAccessRepository->getForClient($publicKey)->allows(SupportModule::NAME)) {
            throw new SupportAccessDeniedException('Support module is not available for this client');
        }

        if (!$this->settingsRepository->getForClient($publicKey)->enabled) {
            throw new SupportAccessDeniedException('Support module is disabled');
        }
    }
}
