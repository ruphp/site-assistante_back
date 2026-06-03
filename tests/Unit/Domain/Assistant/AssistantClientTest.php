<?php

namespace tests\Unit\Domain\Assistant;

use app\Domain\Assistant\AssistantClient;
use PHPUnit\Framework\TestCase;

final class AssistantClientTest extends TestCase
{
    public function testReturnsEnabledModules(): void
    {
        $client = new AssistantClient(12, [
            'widget_modules' => ['courses', 'support'],
        ]);

        self::assertSame(['courses', 'support'], $client->enabledModules());
    }

    public function testParsesAllowedHostsFromDomains(): void
    {
        $client = new AssistantClient(12, [
            'domain' => 'https://example.com,sub.example.org/path, ,',
        ]);

        self::assertSame(['example.com', 'sub.example.org'], $client->allowedHosts());
    }

    public function testChecksAllowedHost(): void
    {
        $client = new AssistantClient(12, [
            'domain' => 'https://example.com,https://sub.example.org/path',
        ]);

        self::assertTrue($client->allowsHost('example.com'));
        self::assertFalse($client->allowsHost('evil.example'));
    }
}
