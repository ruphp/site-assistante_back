<?php

namespace app\Modules\Support\Presentation\Http;

use app\Modules\Support\Domain\SupportPhoneNumber;

final class SupportPhoneLinkFormatter
{
    public static function phone(string $phone): string
    {
        $normalized = SupportPhoneNumber::normalize($phone);
        if ($normalized === null) {
            return self::encode($phone);
        }

        return self::link($phone, $normalized);
    }

    public static function message(string $text): string
    {
        $matches = SupportPhoneNumber::matches($text);
        if ($matches === []) {
            return self::encode($text);
        }

        $result = '';
        $cursor = 0;
        foreach ($matches as $match) {
            $result .= self::encode(substr($text, $cursor, $match['offset'] - $cursor));
            $result .= self::link($match['display'], $match['normalized']);
            $cursor = $match['offset'] + $match['length'];
        }

        return $result . self::encode(substr($text, $cursor));
    }

    private static function link(string $label, string $normalized): string
    {
        return sprintf(
            '<a class="sw-support-phone-link" href="tel:%s" title="Позвонить">%s</a>',
            self::encode($normalized),
            self::encode($label),
        );
    }

    private static function encode(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
