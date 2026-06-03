<?php

namespace tests\Unit\Application\Assistant;

use app\Application\Assistant\AssistantAccessGuard;
use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Exception\AssistantAccessDeniedException;
use app\Domain\Assistant\AssistantClient;
use app\Domain\Assistant\AssistantContext;
use app\Domain\Assistant\AssistantUserContext;
use PHPUnit\Framework\TestCase;

final class AssistantAccessGuardTest extends TestCase
{
    public function testAllowsRequestWithoutOrigin(): void
    {
        $guard = new AssistantAccessGuard();

        $guard->assertAllowed($this->context('https://client.test'), new AssistantRequestContext());

        self::assertTrue(true);
    }

    public function testAllowsConfiguredOrigin(): void
    {
        $guard = new AssistantAccessGuard();

        $guard->assertAllowed($this->context('https://client.test'), new AssistantRequestContext(originHost: 'client.test'));

        self::assertTrue(true);
    }

    public function testDeniesForeignOrigin(): void
    {
        $guard = new AssistantAccessGuard();

        $this->expectException(AssistantAccessDeniedException::class);

        $guard->assertAllowed($this->context('https://client.test'), new AssistantRequestContext(originHost: 'evil.test'));
    }

    public function testAllowsAnyOriginWhenClientHasNoDomainsConfigured(): void
    {
        $guard = new AssistantAccessGuard();

        $guard->assertAllowed($this->context(''), new AssistantRequestContext(originHost: 'any.test'));

        self::assertTrue(true);
    }

    private function context(string $domain): AssistantContext
    {
        return new AssistantContext(
            new AssistantClient(10, ['domain' => $domain]),
            new AssistantUserContext(1, [], []),
        );
    }
}
