<?php

namespace tests\Unit\Module\Support\Domain;

use app\Modules\Support\Domain\SupportEntryPoint;
use PHPUnit\Framework\TestCase;

final class SupportEntryPointTest extends TestCase
{
    public function testInstructionIsSupportedAction(): void
    {
        self::assertSame(
            SupportEntryPoint::RESPONSE_INSTRUCTION,
            SupportEntryPoint::normalizeResponseType(SupportEntryPoint::RESPONSE_INSTRUCTION),
        );
    }

    public function testUnknownActionFallsBackToAnswer(): void
    {
        self::assertSame(
            SupportEntryPoint::RESPONSE_ANSWER,
            SupportEntryPoint::normalizeResponseType('unknown'),
        );
    }
}
