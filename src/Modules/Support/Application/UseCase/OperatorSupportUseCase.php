<?php

namespace app\Modules\Support\Application\UseCase;

use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportConversationListResponse;
use app\Modules\Support\Application\Dto\SupportMessageListResponse;
use app\Modules\Support\Application\Exception\SupportAccessDeniedException;
use app\Modules\Support\Application\Exception\SupportConversationNotFoundException;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportModule;

final class OperatorSupportUseCase
{
    public function __construct(
        private readonly SupportConversationRepositoryInterface $conversations,
        private readonly SupportMessageRepositoryInterface $messages,
        private readonly ClientModuleAccessRepositoryInterface $moduleAccess,
    ) {
    }

    public function listConversations(int $publicKey, ?string $status = SupportConversation::STATUS_OPEN): SupportConversationListResponse
    {
        $this->assertModuleAvailable($publicKey);

        return new SupportConversationListResponse(
            $this->conversations->listForClient($publicKey, $status),
        );
    }

    public function listMessages(int $publicKey, int $conversationId): SupportMessageListResponse
    {
        $this->assertConversationExists($publicKey, $conversationId);

        return new SupportMessageListResponse(
            $this->messages->listForConversation($publicKey, $conversationId),
        );
    }

    public function reply(int $publicKey, int $conversationId, int $operatorId, string $body): void
    {
        $body = trim($body);
        if ($body === '') {
            throw new \InvalidArgumentException('Message body is required');
        }

        $this->assertConversationExists($publicKey, $conversationId);
        $this->messages->addOperatorMessage($publicKey, $conversationId, $operatorId, $body);
    }

    private function assertConversationExists(int $publicKey, int $conversationId): void
    {
        $this->assertModuleAvailable($publicKey);

        if ($this->conversations->getForClient($publicKey, $conversationId) === null) {
            throw new SupportConversationNotFoundException('Conversation not found');
        }
    }

    private function assertModuleAvailable(int $publicKey): void
    {
        if (!$this->moduleAccess->getForClient($publicKey)->allows(SupportModule::NAME)) {
            throw new SupportAccessDeniedException('Support module is not available for this client');
        }
    }
}
