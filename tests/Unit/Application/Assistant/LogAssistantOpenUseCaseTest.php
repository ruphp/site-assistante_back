<?php

namespace tests\Unit\Application\Assistant;

use app\Application\Assistant\AssistantAccessGuard;
use app\Application\Assistant\AssistantUsageLogService;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Contract\AssistantEventLoggerInterface;
use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Dto\LogAssistantOpenRequest;
use app\Application\Assistant\Exception\AssistantAccessDeniedException;
use app\Application\Assistant\UseCase\LogAssistantOpenUseCase;
use app\Domain\Assistant\AssistantClient;
use app\Domain\Assistant\AssistantContext;
use app\Domain\Assistant\AssistantUserContext;
use PHPUnit\Framework\TestCase;

final class LogAssistantOpenUseCaseTest extends TestCase
{
    public function testLogsOpenEvent(): void
    {
        $logger = new FakeAssistantEventLogger();
        $useCase = new LogAssistantOpenUseCase(
            new FakeLogAssistantContextRepository($this->context()),
            new AssistantAccessGuard(),
            new AssistantUsageLogService($logger),
        );

        $result = $useCase->log(new LogAssistantOpenRequest(10, new AssistantRequestContext(originHost: 'client.test')));

        self::assertSame(7, $result);
        self::assertSame(1, $logger->openLogCount);
    }

    public function testDeniesForeignOriginBeforeLogging(): void
    {
        $logger = new FakeAssistantEventLogger();
        $useCase = new LogAssistantOpenUseCase(
            new FakeLogAssistantContextRepository($this->context()),
            new AssistantAccessGuard(),
            new AssistantUsageLogService($logger),
        );

        $this->expectException(AssistantAccessDeniedException::class);

        try {
            $useCase->log(new LogAssistantOpenRequest(10, new AssistantRequestContext(originHost: 'evil.test')));
        } finally {
            self::assertSame(0, $logger->openLogCount);
        }
    }

    private function context(): AssistantContext
    {
        return new AssistantContext(
            new AssistantClient(10, [
                'domain' => 'https://client.test',
                'widget_modules' => [],
            ]),
            new AssistantUserContext(1, [], []),
        );
    }
}

final class FakeLogAssistantContextRepository implements AssistantContextRepositoryInterface
{
    public function __construct(
        private readonly AssistantContext $context,
    ) {
    }

    public function getByPublicKey(int $publicKey, ?AssistantRequestContext $requestContext = null): AssistantContext
    {
        return $this->context;
    }
}

final class FakeAssistantEventLogger implements AssistantEventLoggerInterface
{
    public int $openLogCount = 0;

    public function logOpen(AssistantContext $context): int
    {
        $this->openLogCount++;

        return 7;
    }

    public function logUsage(AssistantContext $context, string $type): int
    {
        return 0;
    }
}
