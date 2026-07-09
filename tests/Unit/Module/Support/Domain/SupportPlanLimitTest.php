<?php

namespace tests\Unit\Module\Support\Domain;

use app\Modules\Support\Domain\SupportPlanLimit;
use PHPUnit\Framework\TestCase;

final class SupportPlanLimitTest extends TestCase
{
    public function testFreeLimitAllowsUsageBelowMonthlyLimit(): void
    {
        $limit = SupportPlanLimit::free();

        self::assertTrue($limit->canStartConversation(299));
        self::assertTrue($limit->canSendMessage(2999));
    }

    public function testFreeLimitDeniesUsageAtMonthlyLimit(): void
    {
        $limit = SupportPlanLimit::free();

        self::assertFalse($limit->canStartConversation(300));
        self::assertFalse($limit->canSendMessage(3000));
    }

    public function testPlanSpecificDailyReplyLimits(): void
    {
        self::assertSame(30, SupportPlanLimit::free()->maxOperatorRepliesPerDay);
        self::assertSame(200, SupportPlanLimit::start()->maxOperatorRepliesPerDay);
        self::assertSame(500, SupportPlanLimit::pro()->maxOperatorRepliesPerDay);
    }

    public function testPlanSpecificMonthlyLimits(): void
    {
        self::assertSame(300, SupportPlanLimit::free()->maxConversationsPerMonth);
        self::assertSame(3000, SupportPlanLimit::free()->maxMessagesPerMonth);

        self::assertSame(3000, SupportPlanLimit::start()->maxConversationsPerMonth);
        self::assertSame(30000, SupportPlanLimit::start()->maxMessagesPerMonth);

        self::assertSame(10000, SupportPlanLimit::pro()->maxConversationsPerMonth);
        self::assertSame(100000, SupportPlanLimit::pro()->maxMessagesPerMonth);
    }
}
