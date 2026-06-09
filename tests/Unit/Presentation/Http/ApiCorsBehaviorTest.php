<?php

namespace tests\Unit\Presentation\Http;

use app\Presentation\Http\ApiCorsBehavior;
use PHPUnit\Framework\TestCase;

final class ApiCorsBehaviorTest extends TestCase
{
    public function testAssistantApiCorsUsesExplicitMethodsAndHeaders(): void
    {
        $behavior = ApiCorsBehavior::assistantApi();
        $cors = $behavior['cors'];

        self::assertSame(['GET', 'POST', 'OPTIONS'], $cors['Access-Control-Request-Method']);
        self::assertContains('Content-Type', $cors['Access-Control-Request-Headers']);
        self::assertContains('X-Requested-With', $cors['Access-Control-Allow-Headers']);
        self::assertSame([], $cors['Access-Control-Expose-Headers']);
    }
}
