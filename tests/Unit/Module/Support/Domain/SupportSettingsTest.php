<?php

namespace tests\Unit\Module\Support\Domain;

use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Support\Domain\SupportSettings;
use PHPUnit\Framework\TestCase;

final class SupportSettingsTest extends TestCase
{
    public function testActiveTrialKeepsStartPlan(): void
    {
        $settings = new SupportSettings(
            publicKey: 1,
            plan: SupportPlan::START,
            planExpiresAt: '2026-09-15 12:00:00',
            trialStartedAt: '2026-09-05 12:00:00',
        );

        $effective = $settings->effective(new \DateTimeImmutable('2026-09-10 12:00:00'));

        self::assertSame(SupportPlan::START, $effective->plan);
        self::assertTrue($effective->isTrialActive(new \DateTimeImmutable('2026-09-10 12:00:00')));
    }

    public function testExpiredTrialBecomesFreeWithoutLosingTrialMarker(): void
    {
        $settings = new SupportSettings(
            publicKey: 1,
            plan: SupportPlan::START,
            planExpiresAt: '2026-09-15 12:00:00',
            trialStartedAt: '2026-09-05 12:00:00',
        );

        $effective = $settings->effective(new \DateTimeImmutable('2026-09-15 12:00:01'));

        self::assertSame(SupportPlan::FREE, $effective->plan);
        self::assertSame('2026-09-05 12:00:00', $effective->trialStartedAt);
    }
}
