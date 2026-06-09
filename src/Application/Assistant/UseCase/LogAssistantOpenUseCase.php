<?php

namespace app\Application\Assistant\UseCase;

use app\Application\Assistant\AssistantAccessGuard;
use app\Application\Assistant\AssistantUsageLogService;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Dto\LogAssistantOpenRequest;

final class LogAssistantOpenUseCase implements LogAssistantOpenUseCaseInterface
{
    public function __construct(
        private readonly AssistantContextRepositoryInterface $assistantContextRepository,
        private readonly AssistantAccessGuard $accessGuard,
        private readonly AssistantUsageLogService $usageLogService,
    ) {
    }

    public function log(LogAssistantOpenRequest $request): int
    {
        $context = $this->assistantContextRepository->getByPublicKey(
            $request->publicKey,
            $request->requestContext,
        );

        $this->accessGuard->assertAllowed($context, $request->requestContext);

        return $this->usageLogService->logOpen($context);
    }
}
