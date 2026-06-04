<?php

namespace tests\Unit\Module\Support\Application;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\Dto\SendSupportMessageRequest;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Application\Exception\SupportConversationNotFoundException;
use app\Modules\Support\Application\Exception\SupportLimitExceededException;
use app\Modules\Support\Application\UseCase\SendSupportMessageUseCase;
use app\Modules\Support\Application\UseCase\SupportAccessGuard;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;
use PHPUnit\Framework\TestCase;

final class SendSupportMessageUseCaseTest extends TestCase
{
    public function testSendsMessageToOpenVisitorConversation(): void
    {
        $conversations = new SendFakeSupportConversationRepository();
        $conversations->conversations[] = new SupportConversation(5, 10, 'visitor-1');
        $messages = new SendFakeSupportMessageRepository();
        $usage = new SendFakeSupportUsageRepository();
        $useCase = new SendSupportMessageUseCase($this->accessGuard(), $conversations, $messages, $usage);

        $response = $useCase->send(new SendSupportMessageRequest(
            10,
            5,
            new SupportVisitorContext(visitorId: 'visitor-1'),
            'Есть вопрос',
        ));

        self::assertSame('Есть вопрос', $response->message->body);
        self::assertSame(1, $usage->messageCount);
    }

    public function testDeniesForeignVisitorConversation(): void
    {
        $conversations = new SendFakeSupportConversationRepository();
        $conversations->conversations[] = new SupportConversation(5, 10, 'visitor-1');
        $useCase = new SendSupportMessageUseCase(
            $this->accessGuard(),
            $conversations,
            new SendFakeSupportMessageRepository(),
            new SendFakeSupportUsageRepository(),
        );

        $this->expectException(SupportConversationNotFoundException::class);

        $useCase->send(new SendSupportMessageRequest(
            10,
            5,
            new SupportVisitorContext(visitorId: 'visitor-2'),
            'Чужой диалог',
        ));
    }

    public function testDeniesMessageWhenFreeLimitIsExceeded(): void
    {
        $conversations = new SendFakeSupportConversationRepository();
        $conversations->conversations[] = new SupportConversation(5, 10, 'visitor-1');
        $usage = new SendFakeSupportUsageRepository();
        $usage->messageCount = 1000;
        $useCase = new SendSupportMessageUseCase(
            $this->accessGuard(),
            $conversations,
            new SendFakeSupportMessageRepository(),
            $usage,
        );

        $this->expectException(SupportLimitExceededException::class);

        $useCase->send(new SendSupportMessageRequest(
            10,
            5,
            new SupportVisitorContext(visitorId: 'visitor-1'),
            'Лимит уже выбран',
        ));
    }

    private function accessGuard(): SupportAccessGuard
    {
        return $this->createStub(SupportAccessGuard::class);
    }
}

final class SendFakeSupportConversationRepository implements SupportConversationRepositoryInterface
{
    public array $conversations = [];

    public function create(int $publicKey, string $visitorId): SupportConversation
    {
        $conversation = new SupportConversation(1, $publicKey, $visitorId);
        $this->conversations[] = $conversation;

        return $conversation;
    }

    public function getOpenForVisitor(int $publicKey, int $conversationId, string $visitorId): ?SupportConversation
    {
        foreach ($this->conversations as $conversation) {
            if ($conversation->id === $conversationId && $conversation->visitorId === $visitorId) {
                return $conversation;
            }
        }

        return null;
    }

    public function getForClient(int $publicKey, int $conversationId): ?SupportConversation
    {
        foreach ($this->conversations as $conversation) {
            if ($conversation->id === $conversationId && $conversation->publicKey === $publicKey) {
                return $conversation;
            }
        }

        return null;
    }

    public function listForClient(int $publicKey, ?string $status = null, int $limit = 50): array
    {
        return array_values(array_filter(
            $this->conversations,
            static fn(SupportConversation $conversation): bool => $conversation->publicKey === $publicKey,
        ));
    }
}

final class SendFakeSupportMessageRepository implements SupportMessageRepositoryInterface
{
    public array $messages = [];

    public function addVisitorMessage(int $publicKey, int $conversationId, string $visitorId, string $body): SupportMessage
    {
        $message = new SupportMessage(1, $conversationId, $publicKey, SupportMessage::SENDER_VISITOR, $visitorId, $body);
        $this->messages[] = $message;

        return $message;
    }

    public function addOperatorMessage(int $publicKey, int $conversationId, int $operatorId, string $body): SupportMessage
    {
        $message = new SupportMessage(1, $conversationId, $publicKey, SupportMessage::SENDER_OPERATOR, (string)$operatorId, $body);
        $this->messages[] = $message;

        return $message;
    }

    public function listForConversation(int $publicKey, int $conversationId, ?int $afterId = null): array
    {
        return $this->messages;
    }
}

final class SendFakeSupportUsageRepository implements SupportUsageRepositoryInterface
{
    public int $conversationCount = 0;
    public int $messageCount = 0;

    public function monthlyConversationCount(int $publicKey, \DateTimeImmutable $month): int
    {
        return $this->conversationCount;
    }

    public function monthlyMessageCount(int $publicKey, \DateTimeImmutable $month): int
    {
        return $this->messageCount;
    }

    public function incrementConversations(int $publicKey, \DateTimeImmutable $month): void
    {
        $this->conversationCount++;
    }

    public function incrementMessages(int $publicKey, \DateTimeImmutable $month): void
    {
        $this->messageCount++;
    }
}
