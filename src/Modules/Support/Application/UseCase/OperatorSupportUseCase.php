<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportRealtimePublisherInterface;
use app\Modules\Support\Application\Contract\SupportReplyNotifierInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportConversationListResponse;
use app\Modules\Support\Application\Dto\SupportConversationResponse;
use app\Modules\Support\Application\Dto\SupportMessageListResponse;
use app\Modules\Support\Application\Exception\SupportLimitExceededException;
use app\Modules\Support\Application\Exception\SupportConversationNotFoundException;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;
use app\Modules\Support\Domain\SupportPlanLimit;

final class OperatorSupportUseCase
{
    public function __construct(
        private readonly SupportConversationRepositoryInterface $conversations,
        private readonly SupportMessageRepositoryInterface $messages,
        private readonly SupportReplyNotifierInterface $replyNotifier,
        private readonly SupportUsageRepositoryInterface $usage,
        private readonly SupportSettingsRepositoryInterface $settings,
        private readonly SupportRealtimePublisherInterface $realtimePublisher,
    ) {
    }

    public function listConversations(int $publicKey, ?string $status = SupportConversation::STATUS_OPEN): SupportConversationListResponse
    {
        return new SupportConversationListResponse(
            $this->conversations->listForClient($publicKey, $status),
            $this->operatorReplyLimit($publicKey),
        );
    }

    public function listMessages(int $publicKey, int $conversationId): SupportMessageListResponse
    {
        $conversation = $this->assertConversationExists($publicKey, $conversationId);

        return new SupportMessageListResponse(
            $this->messages->listForConversation($conversation->publicKey, $conversationId),
        );
    }

    public function conversation(int $publicKey, int $conversationId): SupportConversationResponse
    {
        return new SupportConversationResponse(
            $this->assertConversationExists($publicKey, $conversationId),
        );
    }

    public function reply(int $publicKey, int $conversationId, int $operatorId, string $body): SupportMessage
    {
        $body = trim($body);
        if ($body === '') {
            throw new \InvalidArgumentException('Message body is required');
        }

        $conversation = $this->assertConversationExists($publicKey, $conversationId);
        $today = new \DateTimeImmutable('today');
        $month = new \DateTimeImmutable('first day of this month 00:00:00');
        $limit = SupportPlanLimit::forPlan($this->settings->getForClient($conversation->publicKey)->plan);
        if (!$limit->canOperatorReply($this->usage->dailyOperatorReplyCount($conversation->publicKey, $today))) {
            throw new SupportLimitExceededException('Operator daily reply limit exceeded');
        }

        $message = $this->messages->addOperatorMessage($conversation->publicKey, $conversationId, $operatorId, $body);
        $this->conversations->markOperatorReply($conversation->publicKey, $conversationId);
        $this->usage->incrementOperatorReplies($conversation->publicKey, $today);
        $this->usage->incrementMessages($conversation->publicKey, $month);
        $this->replyNotifier->notifyOperatorReply($conversation, $message);
        $this->realtimePublisher->publishMessage($conversation, $message);

        return $message;
    }

    public function closeConversation(int $publicKey, int $conversationId): void
    {
        $this->assertConversationExists($publicKey, $conversationId);
        $this->conversations->closeForClient($publicKey, $conversationId);
    }

    public function deleteConversation(int $publicKey, int $conversationId): void
    {
        $this->assertConversationExists($publicKey, $conversationId);
        $this->conversations->deleteForClient($publicKey, $conversationId);
    }

    private function assertConversationExists(int $publicKey, int $conversationId): SupportConversation
    {
        $conversation = $this->conversations->getForClient($publicKey, $conversationId);
        if ($conversation === null) {
            throw new SupportConversationNotFoundException('Conversation not found');
        }

        return $conversation;
    }

    private function operatorReplyLimit(int $publicKey): array
    {
        $today = new \DateTimeImmutable('today');
        $limit = SupportPlanLimit::forPlan($this->settings->getForClient($publicKey)->plan);
        $used = $this->usage->dailyOperatorReplyCount($publicKey, $today);
        $remaining = max(0, $limit->maxOperatorRepliesPerDay - $used);

        return [
            'operator_replies_per_day' => $limit->maxOperatorRepliesPerDay,
            'used_operator_replies_today' => $used,
            'operator_replies_remaining_today' => $remaining,
            'operator_reply_limit_exhausted' => $remaining <= 0,
        ];
    }
}
