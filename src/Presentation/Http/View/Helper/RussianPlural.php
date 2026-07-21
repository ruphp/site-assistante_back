<?php

namespace app\Presentation\Http\View\Helper;

final class RussianPlural
{
    public static function word(int $number, string $one, string $few, string $many): string
    {
        $mod100 = abs($number) % 100;
        $mod10 = $mod100 % 10;

        if ($mod100 >= 11 && $mod100 <= 19) {
            return $many;
        }

        if ($mod10 === 1) {
            return $one;
        }

        if ($mod10 >= 2 && $mod10 <= 4) {
            return $few;
        }

        return $many;
    }
}
