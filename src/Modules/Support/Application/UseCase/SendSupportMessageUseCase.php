<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\Dto\SendSupportMessageRequest;
use app\Modules\Support\Application\Dto\SupportMessageResponse;
use app\Modules\Support\Application\Exception\SupportConversationNotFoundException;
use app\Modules\Support\Application\Exception\SupportLimitExceededException;
use app\Modules\Support\Domain\SupportPlanLimit;

final class SendSupportMessageUseCase implements SendSupportMessageUseCaseInterface
{
    public function __construct(
        private readonly SupportAccessGuard $accessGuard,
        private readonly SupportConversationRepositoryInterface $conversations,
        private readonly SupportMessageRepositoryInterface $messages,
        private readonly SupportUsageRepositoryInterface $usage,
    ) {
    }

    public function send(SendSupportMessageRequest $request): SupportMessageResponse
    {
        $body = trim($request->body);
        if ($body === '') {
            throw new \InvalidArgumentException('Message body is required');
        }

        $this->accessGuard->assertAvailable($request->publicKey, $request->context);
        $conversation = $this->conversations->getOpenForVisitor(
            $request->publicKey,
            $request->conversationId,
            $request->context->resolvedVisitorId(),
        );

        if ($conversation === null) {
            throw new SupportConversationNotFoundException('Conversation not found');
        }

        $month = new \DateTimeImmutable('first day of this month 00:00:00');
        if (!SupportPlanLimit::free()->canSendMessage($this->usage->monthlyMessageCount($request->publicKey, $month))) {
            throw new SupportLimitExceededException('Message monthly limit exceeded');
        }

        $message = $this->messages->addVisitorMessage(
            $request->publicKey,
            $request->conversationId,
            $request->context->resolvedVisitorId(),
            $body,
        );
        $this->usage->incrementMessages($request->publicKey, $month);

        return new SupportMessageResponse($message);
    }
}
