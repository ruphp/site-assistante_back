<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportManagerNotifierInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportRealtimePublisherInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
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
        private readonly SupportSettingsRepositoryInterface $settings,
        private readonly SupportManagerNotifierInterface $managerNotifier,
        private readonly SupportRealtimePublisherInterface $realtimePublisher,
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
        $limit = SupportPlanLimit::forPlan($this->settings->getForClient($request->publicKey)->plan);
        if (!$limit->canSendMessage($this->usage->monthlyMessageCount($request->publicKey, $month))) {
            throw new SupportLimitExceededException('Message monthly limit exceeded');
        }

        $message = $this->messages->addVisitorMessage(
            $request->publicKey,
            $request->conversationId,
            $request->context->resolvedVisitorId(),
            $body,
        );
        $this->usage->incrementMessages($request->publicKey, $month);
        $this->managerNotifier->notifyVisitorMessage($conversation, $message);
        $this->realtimePublisher->publishMessage($conversation, $message);

        return new SupportMessageResponse($message);
    }
}
