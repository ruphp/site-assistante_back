<?php

namespace tests\Unit\Module\Support\Application;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportEntryPointRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportManagerNotifierInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportRealtimePublisherInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\Dto\StartSupportConversationRequest;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Application\Exception\SupportLimitExceededException;
use app\Modules\Support\Application\UseCase\StartSupportConversationUseCase;
use app\Modules\Support\Application\UseCase\SupportAccessGuard;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportEntryPoint;
use app\Modules\Support\Domain\SupportMessage;
use app\Modules\Support\Domain\SupportSettings;
use PHPUnit\Framework\TestCase;

final class StartSupportConversationUseCaseTest extends TestCase
{
    public function testStartsConversationAndStoresFirstMessage(): void
    {
        $conversations = new FakeSupportConversationRepository();
        $messages = new FakeSupportMessageRepository();
        $entryPoints = new FakeSupportEntryPointRepository();
        $usage = new FakeSupportUsageRepository();
        $notifier = new FakeSupportManagerNotifier();
        $realtime = new FakeSupportRealtimePublisher();
        $useCase = new StartSupportConversationUseCase(
            $this->accessGuard(),
            $conversations,
            $entryPoints,
            $messages,
            $usage,
            new FakeSupportSettingsRepository(),
            $notifier,
            $realtime,
        );

        $response = $useCase->start(new StartSupportConversationRequest(
            10,
            new SupportVisitorContext(visitorId: 'visitor-1'),
            'Здравствуйте',
        ));

        self::assertSame(1, $response->conversation->id);
        self::assertSame('visitor-1', $response->conversation->visitorId);
        self::assertSame(1, $usage->conversationCount);
        self::assertSame(1, $usage->messageCount);
        self::assertSame('Здравствуйте', $messages->messages[0]->body);
        self::assertSame(1, $notifier->count);
        self::assertSame(1, $realtime->count);
    }

    public function testDeniesConversationWhenFreeLimitIsExceeded(): void
    {
        $usage = new FakeSupportUsageRepository();
        $usage->conversationCount = 100;
        $useCase = new StartSupportConversationUseCase(
            $this->accessGuard(),
            new FakeSupportConversationRepository(),
            new FakeSupportEntryPointRepository(),
            new FakeSupportMessageRepository(),
            $usage,
            new FakeSupportSettingsRepository(),
            new FakeSupportManagerNotifier(),
            new FakeSupportRealtimePublisher(),
        );

        $this->expectException(SupportLimitExceededException::class);

        $useCase->start(new StartSupportConversationRequest(10, new SupportVisitorContext(visitorId: 'visitor-1')));
    }

    public function testStartsConversationWithEntryPointPriority(): void
    {
        $conversations = new FakeSupportConversationRepository();
        $entryPoints = new FakeSupportEntryPointRepository();
        $entryPoints->entryPoints[] = new SupportEntryPoint(7, 10, 'Не работает сервис', priority: 5);
        $useCase = new StartSupportConversationUseCase(
            $this->accessGuard(),
            $conversations,
            $entryPoints,
            new FakeSupportMessageRepository(),
            new FakeSupportUsageRepository(),
            new FakeSupportSettingsRepository(),
            new FakeSupportManagerNotifier(),
            new FakeSupportRealtimePublisher(),
        );

        $response = $useCase->start(new StartSupportConversationRequest(
            10,
            new SupportVisitorContext(visitorId: 'visitor-1'),
            'Нужна помощь',
            7,
        ));

        self::assertSame(7, $response->conversation->entryPointId);
        self::assertSame(5, $response->conversation->priority);
    }

    public function testUsesEmailAsVisitorIdentityWhenUserIdIsMissing(): void
    {
        $context = new SupportVisitorContext(visitorEmail: 'USER@Example.COM');

        self::assertSame('email:user@example.com', $context->resolvedVisitorId());
    }

    private function accessGuard(): SupportAccessGuard
    {
        return $this->createStub(SupportAccessGuard::class);
    }
}

final class FakeSupportConversationRepository implements SupportConversationRepositoryInterface
{
    public array $conversations = [];

    public function create(int $publicKey, SupportVisitorContext $context, ?SupportEntryPoint $entryPoint = null): SupportConversation
    {
        $conversation = new SupportConversation(
            id: 1,
            publicKey: $publicKey,
            visitorId: $context->resolvedVisitorId(),
            visitorEmail: $context->visitorEmail,
            pageUrl: $context->pageUrl,
            entryPointId: $entryPoint?->id,
            priority: $entryPoint?->priority ?? 0,
        );
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

final class FakeSupportEntryPointRepository implements SupportEntryPointRepositoryInterface
{
    public array $entryPoints = [];

    public function listForClient(int $publicKey, bool $enabledOnly = false): array
    {
        return array_values(array_filter(
            $this->entryPoints,
            static fn(SupportEntryPoint $entryPoint): bool => $entryPoint->publicKey === $publicKey
                && (!$enabledOnly || $entryPoint->enabled),
        ));
    }

    public function countForClient(int $publicKey): int
    {
        return count($this->listForClient($publicKey));
    }

    public function findForClient(int $publicKey, int $id): ?SupportEntryPoint
    {
        foreach ($this->entryPoints as $entryPoint) {
            if ($entryPoint->publicKey === $publicKey && $entryPoint->id === $id) {
                return $entryPoint;
            }
        }

        return null;
    }

    public function save(SupportEntryPoint $entryPoint): bool
    {
        $this->entryPoints[] = $entryPoint;

        return true;
    }

    public function deleteForClient(int $publicKey, int $id): bool
    {
        return true;
    }
}

final class FakeSupportSettingsRepository implements SupportSettingsRepositoryInterface
{
    public function getForClient(int $publicKey): SupportSettings
    {
        return new SupportSettings($publicKey);
    }

    public function save(SupportSettings $settings): bool
    {
        return true;
    }
}

final class FakeSupportMessageRepository implements SupportMessageRepositoryInterface
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

final class FakeSupportUsageRepository implements SupportUsageRepositoryInterface
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

final class FakeSupportManagerNotifier implements SupportManagerNotifierInterface
{
    public int $count = 0;

    public function notifyVisitorMessage(SupportConversation $conversation, SupportMessage $message): void
    {
        $this->count++;
    }
}

final class FakeSupportRealtimePublisher implements SupportRealtimePublisherInterface
{
    public int $count = 0;

    public function publishMessage(SupportConversation $conversation, SupportMessage $message): void
    {
        $this->count++;
    }
}
