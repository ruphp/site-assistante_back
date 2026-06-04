<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Dto\ListSupportMessagesRequest;
use app\Modules\Support\Application\Dto\SupportMessageListResponse;
use app\Modules\Support\Application\Exception\SupportConversationNotFoundException;

final class ListSupportMessagesUseCase implements ListSupportMessagesUseCaseInterface
{
    public function __construct(
        private readonly SupportAccessGuard $accessGuard,
        private readonly SupportConversationRepositoryInterface $conversations,
        private readonly SupportMessageRepositoryInterface $messages,
    ) {
    }

    public function list(ListSupportMessagesRequest $request): SupportMessageListResponse
    {
        $this->accessGuard->assertAvailable($request->publicKey, $request->context);
        $conversation = $this->conversations->getOpenForVisitor(
            $request->publicKey,
            $request->conversationId,
            $request->context->resolvedVisitorId(),
        );

        if ($conversation === null) {
            throw new SupportConversationNotFoundException('Conversation not found');
        }

        return new SupportMessageListResponse(
            $this->messages->listForConversation($request->publicKey, $request->conversationId, $request->afterId),
        );
    }
}
