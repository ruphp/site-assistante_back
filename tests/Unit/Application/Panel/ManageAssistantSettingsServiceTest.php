<?php

namespace tests\Unit\Application\Panel;

use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Application\Panel\Contract\AssistantDesignStorageInterface;
use app\Application\Panel\Contract\AssistantParamsRepositoryInterface;
use app\Application\Panel\ManageAssistantSettingsService;
use app\Domain\Client\ClientModuleAccess;
use PHPUnit\Framework\TestCase;

final class ManageAssistantSettingsServiceTest extends TestCase
{
    public function testSaveParamsRemovesModuleFieldsWithoutAccess(): void
    {
        $params = new FakeAssistantParamsRepository();
        $service = new ManageAssistantSettingsService(
            $params,
            new FakeAssistantDesignStorage(),
            new FakeClientModuleAccessRepositoryForSettings([]),
        );

        self::assertTrue($service->saveParams(10, [
            'Params' => [
                'domain' => 'https://client.test',
                'default_answer' => 'hidden chatbot field',
                'chatbot_bigdata_system_id' => 42,
                'chatbot_bigdata_is_active' => 1,
            ],
        ]));

        self::assertArrayNotHasKey('default_answer', $params->lastPost['Params']);
        self::assertArrayNotHasKey('chatbot_bigdata_system_id', $params->lastPost['Params']);
        self::assertArrayNotHasKey('chatbot_bigdata_is_active', $params->lastPost['Params']);
        self::assertSame('https://client.test', $params->lastPost['Params']['domain']);
    }

    public function testSaveParamsKeepsFieldsWithModuleAccess(): void
    {
        $params = new FakeAssistantParamsRepository();
        $service = new ManageAssistantSettingsService(
            $params,
            new FakeAssistantDesignStorage(),
            new FakeClientModuleAccessRepositoryForSettings(['chatbots', 'bigdata']),
        );

        self::assertTrue($service->saveParams(10, [
            'Params' => [
                'default_answer' => 'hello',
                'chatbot_bigdata_system_id' => 42,
                'chatbot_bigdata_is_active' => 1,
            ],
        ]));

        self::assertSame('hello', $params->lastPost['Params']['default_answer']);
        self::assertSame(42, $params->lastPost['Params']['chatbot_bigdata_system_id']);
        self::assertSame(1, $params->lastPost['Params']['chatbot_bigdata_is_active']);
    }
}

final class FakeAssistantParamsRepository implements AssistantParamsRepositoryInterface
{
    public array $lastPost = [];

    public function findForClient(int $publicKey): mixed
    {
        return new \stdClass();
    }

    public function findOrCreateForClient(int $publicKey): mixed
    {
        return new \stdClass();
    }

    public function saveFromPost(mixed $params, array $post, int $publicKey): bool
    {
        $this->lastPost = $post;

        return true;
    }
}

final class FakeAssistantDesignStorage implements AssistantDesignStorageInterface
{
    public function ensureFiles(int $publicKey): void
    {
    }

    public function getCustomCss(int $publicKey): string
    {
        return '';
    }

    public function getLogoSvg(int $publicKey): string
    {
        return '';
    }

    public function saveCustomCss(int $publicKey, string $customCss): void
    {
    }

    public function saveLogoSvg(int $publicKey, string $logoSvg): void
    {
    }
}

final class FakeClientModuleAccessRepositoryForSettings implements ClientModuleAccessRepositoryInterface
{
    public function __construct(
        private readonly array $modules,
    ) {
    }

    public function getForClient(int $publicKey): ClientModuleAccess
    {
        return new ClientModuleAccess($this->modules);
    }
}
