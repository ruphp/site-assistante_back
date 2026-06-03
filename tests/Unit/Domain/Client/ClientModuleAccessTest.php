<?php

namespace tests\Unit\Domain\Client;

use app\Domain\Client\ClientModuleAccess;
use PHPUnit\Framework\TestCase;

final class ClientModuleAccessTest extends TestCase
{
    public function testFiltersRequestedModulesByGrantedAccess(): void
    {
        $access = new ClientModuleAccess(['courses', 'surveys']);

        self::assertSame(
            ['courses', 'surveys'],
            $access->filterAllowed(['courses', 'chatbots', 'surveys']),
        );
    }

    public function testChecksSingleModuleAccess(): void
    {
        $access = new ClientModuleAccess(['chatbots']);

        self::assertTrue($access->allows('chatbots'));
        self::assertFalse($access->allows('bigdata'));
    }
}
