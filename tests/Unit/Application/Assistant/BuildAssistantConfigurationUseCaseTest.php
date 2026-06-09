<?php

namespace tests\Unit\Application\Assistant;

use app\Application\Assistant\Contract\AssistantConfigurationLoggerInterface;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Application\Assistant\Exception\AssistantAccessDeniedException;
use app\Application\Assistant\UseCase\BuildAssistantConfigurationUseCase;
use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Domain\Assistant\AssistantClient;
use app\Domain\Assistant\AssistantContext;
use app\Domain\Assistant\AssistantUserContext;
use app\Domain\Client\ClientModuleAccess;
use PHPUnit\Framework\TestCase;

final class BuildAssistantConfigurationUseCaseTest extends TestCase
{
    public function testBuildsConfigurationWithEnabledModules(): void
    {
        $context = $this->context([
            'leftbutton' => true,
            'run' => true,
            'design' => 'dark',
            'domain' => 'https://client.test,https://second.test/path',
            'tab_tp_contacts' => true,
            'tp_contacts' => 'Call us',
            'timeout' => 15,
            'server_stp' => 'https://support.test',
            'widget_modules' => ['courses', 'support'],
        ]);

        $useCase = new BuildAssistantConfigurationUseCase(
            new FakeAssistantContextRepository($context),
            new FakeAssistantConfigurationLogger(),
            new FakeClientModuleAccessRepository(['courses', 'support']),
        );

        $response = $useCase->build(new BuildAssistantConfigurationRequest(10))->toArray();

        self::assertSame('left', $response['position']);
        self::assertSame(['client.test', 'second.test'], $response['domain']);
        self::assertSame(0, $response['type_tickets']);
        self::assertSame(['courses', 'support'], $response['modules']);
        self::assertSame('Call us', $response['text_contacts']);
    }

    public function testAddsRedisErrorWhenLoggerFails(): void
    {
        $useCase = new BuildAssistantConfigurationUseCase(
            new FakeAssistantContextRepository($this->context()),
            new FailingAssistantConfigurationLogger(),
            new FakeClientModuleAccessRepository([]),
        );

        $response = $useCase->build(new BuildAssistantConfigurationRequest(10))->toArray();

        self::assertSame(['redis' => 'no connect'], $response['error']);
    }

    public function testPassesRequestContextToRepository(): void
    {
        $requestContext = new AssistantRequestContext(userId: 77);
        $repository = new FakeAssistantContextRepository($this->context());
        $useCase = new BuildAssistantConfigurationUseCase(
            $repository,
            new FakeAssistantConfigurationLogger(),
            new FakeClientModuleAccessRepository([]),
        );

        $useCase->build(new BuildAssistantConfigurationRequest(10, $requestContext));

        self::assertSame($requestContext, $repository->lastRequestContext);
    }

    public function testDeniesConfigurationForForeignOrigin(): void
    {
        $useCase = new BuildAssistantConfigurationUseCase(
            new FakeAssistantContextRepository($this->context(['domain' => 'https://client.test'])),
            new FakeAssistantConfigurationLogger(),
            new FakeClientModuleAccessRepository([]),
        );

        $this->expectException(AssistantAccessDeniedException::class);

        $useCase->build(new BuildAssistantConfigurationRequest(10, new AssistantRequestContext(originHost: 'evil.test')));
    }

    public function testReturnsOnlyModulesAllowedBySuperAdminAndEnabledByClient(): void
    {
        $useCase = new BuildAssistantConfigurationUseCase(
            new FakeAssistantContextRepository($this->context([
                'widget_modules' => ['courses', 'chatbots', 'surveys'],
            ])),
            new FakeAssistantConfigurationLogger(),
            new FakeClientModuleAccessRepository(['courses', 'surveys']),
        );

        $response = $useCase->build(new BuildAssistantConfigurationRequest(10))->toArray();

        self::assertSame(['courses', 'surveys'], $response['modules']);
    }

    private function context(array $params = []): AssistantContext
    {
        return new AssistantContext(
            new AssistantClient(10, $params + [
                'leftbutton' => false,
                'run' => true,
                'design' => 'default',
                'domain' => 'https://client.test',
                'tab_tp_contacts' => false,
                'tp_contacts' => '',
                'timeout' => 0,
                'server_stp' => '',
                'widget_modules' => [],
            ]),
            new AssistantUserContext(1, [], []),
        );
    }
}

final class FakeAssistantContextRepository implements AssistantContextRepositoryInterface
{
    public ?AssistantRequestContext $lastRequestContext = null;

    public function __construct(
        private readonly AssistantContext $context,
    ) {
    }

    public function getByPublicKey(int $publicKey, ?AssistantRequestContext $requestContext = null): AssistantContext
    {
        $this->lastRequestContext = $requestContext;

        return $this->context;
    }
}

final class FakeAssistantConfigurationLogger implements AssistantConfigurationLoggerInterface
{
    public function log(BuildAssistantConfigurationRequest $request, AssistantContext $context): void
    {
    }
}

final class FailingAssistantConfigurationLogger implements AssistantConfigurationLoggerInterface
{
    public function log(BuildAssistantConfigurationRequest $request, AssistantContext $context): void
    {
        throw new \RuntimeException('Redis down');
    }
}

final class FakeClientModuleAccessRepository implements ClientModuleAccessRepositoryInterface
{
    public function __construct(
        private readonly array $allowedModules,
    ) {
    }

    public function getForClient(int $publicKey): ClientModuleAccess
    {
        return new ClientModuleAccess($this->allowedModules);
    }
}
