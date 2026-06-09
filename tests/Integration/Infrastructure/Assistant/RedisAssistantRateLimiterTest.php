<?php

namespace tests\Integration\Infrastructure\Assistant;

use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Exception\AssistantRateLimitExceededException;
use app\Infrastructure\Assistant\RedisAssistantRateLimiter;
use tests\Integration\Support\YiiIntegrationTestCase;

final class RedisAssistantRateLimiterTest extends YiiIntegrationTestCase
{
    public function testAllowsRequestsInsideLimit(): void
    {
        $limiter = new RedisAssistantRateLimiter(10, 2, 60);
        $context = new AssistantRequestContext(remoteAddr: '10.10.10.1');
        $publicKey = random_int(100000, 999999);

        $limiter->hit($publicKey, $context, 'configuration');
        $limiter->hit($publicKey, $context, 'configuration');

        $this->addToAssertionCount(1);
    }

    public function testThrowsWhenVisitorLimitExceeded(): void
    {
        $limiter = new RedisAssistantRateLimiter(10, 2, 60);
        $context = new AssistantRequestContext(remoteAddr: '10.10.10.2');
        $publicKey = random_int(100000, 999999);

        $limiter->hit($publicKey, $context, 'configuration');
        $limiter->hit($publicKey, $context, 'configuration');

        $this->expectException(AssistantRateLimitExceededException::class);

        $limiter->hit($publicKey, $context, 'configuration');
    }

    public function testUsesDifferentBucketsForDifferentActions(): void
    {
        $limiter = new RedisAssistantRateLimiter(10, 1, 60);
        $context = new AssistantRequestContext(remoteAddr: '10.10.10.3');
        $publicKey = random_int(100000, 999999);

        $limiter->hit($publicKey, $context, 'configuration');
        $limiter->hit($publicKey, $context, 'log-open');

        $this->addToAssertionCount(1);
    }

    public function testThrowsWhenClientLimitExceededAcrossVisitors(): void
    {
        $limiter = new RedisAssistantRateLimiter(2, 10, 60);
        $publicKey = random_int(100000, 999999);

        $limiter->hit($publicKey, new AssistantRequestContext(remoteAddr: '10.10.20.1'), 'configuration');
        $limiter->hit($publicKey, new AssistantRequestContext(remoteAddr: '10.10.20.2'), 'configuration');

        $this->expectException(AssistantRateLimitExceededException::class);

        $limiter->hit($publicKey, new AssistantRequestContext(remoteAddr: '10.10.20.3'), 'configuration');
    }
}
