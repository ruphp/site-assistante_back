<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\Dto\StartSupportConversationRequest;
use app\Modules\Support\Application\Dto\SupportConversationResponse;
use app\Modules\Support\Application\Exception\SupportLimitExceededException;
use app\Modules\Support\Domain\SupportPlanLimit;

final class StartSupportConversationUseCase implements StartSupportConversationUseCaseInterface
{
    public function __construct(
        private readonly SupportAccessGuard $accessGuard,
        private readonly SupportConversationRepositoryInterface $conversations,
        private readonly SupportMessageRepositoryInterface $messages,
        private readonly SupportUsageRepositoryInterface $usage,
    ) {
    }

    public function start(StartSupportConversationRequest $request): SupportConversationResponse
    {
        $this->accessGuard->assertAvailable($request->publicKey, $request->context);
        $month = new \DateTimeImmutable('first day of this month 00:00:00');
        $limit = SupportPlanLimit::free();

        if (!$limit->canStartConversation($this->usage->monthlyConversationCount($request->publicKey, $month))) {
            throw new SupportLimitExceededException('Conversation monthly limit exceeded');
        }

        $conversation = $this->conversations->create(
            $request->publicKey,
            $request->context,
        );
        $this->usage->incrementConversations($request->publicKey, $month);

        $firstMessage = trim((string)$request->firstMessage);
        if ($firstMessage !== '') {
            if (!$limit->canSendMessage($this->usage->monthlyMessageCount($request->publicKey, $month))) {
                throw new SupportLimitExceededException('Message monthly limit exceeded');
            }
            $this->messages->addVisitorMessage(
                $request->publicKey,
                (int)$conversation->id,
                $request->context->resolvedVisitorId(),
                $firstMessage,
            );
            $this->usage->incrementMessages($request->publicKey, $month);
        }

        return new SupportConversationResponse($conversation);
    }
}
