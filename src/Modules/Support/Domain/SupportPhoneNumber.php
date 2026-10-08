<?php

namespace app\Modules\Support\Domain;

final class SupportPhoneNumber
{
    private const CANDIDATE_PATTERN = '/(?<![\p{L}\p{N}])(?:\+\s*\d(?:[\s().-]*\d){9,14}|[78](?:[\s().-]*\d){10}|9(?:[\s().-]*\d){9})(?![\p{L}\p{N}])/u';

    public static function normalize(string $value): ?string
    {
        $value = trim($value);
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (str_starts_with($value, '+') && strlen($digits) >= 10 && strlen($digits) <= 15) {
            return '+' . $digits;
        }

        if (strlen($digits) === 11 && $digits[0] === '8') {
            return '+7' . substr($digits, 1);
        }

        if (strlen($digits) === 11 && $digits[0] === '7') {
            return '+' . $digits;
        }

        if (strlen($digits) === 10 && $digits[0] === '9') {
            return '+7' . $digits;
        }

        return null;
    }

    /**
     * @return array<int, array{display: string, normalized: string, offset: int, length: int}>
     */
    public static function matches(string $text): array
    {
        if (!preg_match_all(self::CANDIDATE_PATTERN, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $result = [];
        foreach ($matches[0] as [$display, $offset]) {
            $normalized = self::normalize($display);
            if ($normalized === null) {
                continue;
            }

            $result[] = [
                'display' => $display,
                'normalized' => $normalized,
                'offset' => $offset,
                'length' => strlen($display),
            ];
        }

        return $result;
    }

    /**
     * @return string[]
     */
    public static function extract(string $text): array
    {
        return array_values(array_unique(array_column(self::matches($text), 'normalized')));
    }

    public static function normalizeInText(string $text): string
    {
        $matches = self::matches($text);
        if ($matches === []) {
            return $text;
        }

        $result = '';
        $cursor = 0;
        foreach ($matches as $match) {
            $result .= substr($text, $cursor, $match['offset'] - $cursor);
            $result .= $match['normalized'];
            $cursor = $match['offset'] + $match['length'];
        }

        return $result . substr($text, $cursor);
    }
}
