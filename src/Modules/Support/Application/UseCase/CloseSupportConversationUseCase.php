<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Dto\CloseSupportConversationRequest;
use app\Modules\Support\Application\Exception\SupportConversationNotFoundException;

final class CloseSupportConversationUseCase implements CloseSupportConversationUseCaseInterface
{
    public function __construct(
        private readonly SupportAccessGuard $accessGuard,
        private readonly SupportConversationRepositoryInterface $conversations,
    ) {
    }

    public function close(CloseSupportConversationRequest $request): void
    {
        if ($request->conversationId <= 0) {
            throw new \InvalidArgumentException('Conversation id is required');
        }

        $this->accessGuard->assertAvailable($request->publicKey, $request->context);
        $visitorId = $request->context->resolvedVisitorId();
        $conversation = $this->conversations->getOpenForVisitor(
            $request->publicKey,
            $request->conversationId,
            $visitorId,
        );

        if ($conversation === null) {
            throw new SupportConversationNotFoundException('Conversation not found');
        }

        $this->conversations->closeForClient($request->publicKey, $request->conversationId);
    }
}
