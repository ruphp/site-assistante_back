<?php

namespace tests\Unit\Module\Support\Domain;

use app\Modules\Support\Domain\SupportPlanLimit;
use PHPUnit\Framework\TestCase;

final class SupportPlanLimitTest extends TestCase
{
    public function testFreeLimitAllowsUsageBelowMonthlyLimit(): void
    {
        $limit = SupportPlanLimit::free();

        self::assertTrue($limit->canStartConversation(99));
        self::assertTrue($limit->canSendMessage(999));
    }

    public function testFreeLimitDeniesUsageAtMonthlyLimit(): void
    {
        $limit = SupportPlanLimit::free();

        self::assertFalse($limit->canStartConversation(100));
        self::assertFalse($limit->canSendMessage(1000));
    }

    public function testPlanSpecificDailyReplyLimits(): void
    {
        self::assertSame(10, SupportPlanLimit::free()->maxOperatorRepliesPerDay);
        self::assertSame(100, SupportPlanLimit::start()->maxOperatorRepliesPerDay);
        self::assertSame(300, SupportPlanLimit::pro()->maxOperatorRepliesPerDay);
    }
}
