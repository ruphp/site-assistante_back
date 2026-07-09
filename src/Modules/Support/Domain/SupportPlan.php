<?php

namespace app\Modules\Support\Domain;

final class SupportPlan
{
    public const FREE = 'free';
    public const START = 'start';
    public const PRO = 'pro';

    public static function normalize(string $plan): string
    {
        return in_array($plan, [self::FREE, self::START, self::PRO], true) ? $plan : self::FREE;
    }

    public static function labels(): array
    {
        return [
            self::FREE => 'Free',
            self::START => 'Start',
            self::PRO => 'Pro',
        ];
    }
}
