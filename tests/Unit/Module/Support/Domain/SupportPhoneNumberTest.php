<?php

namespace tests\Unit\Module\Support\Domain;

use app\Modules\Support\Domain\SupportPhoneNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SupportPhoneNumberTest extends TestCase
{
    #[DataProvider('phoneProvider')]
    public function testNormalizesSupportedPhoneFormats(string $input, string $expected): void
    {
        self::assertSame($expected, SupportPhoneNumber::normalize($input));
    }

    public static function phoneProvider(): array
    {
        return [
            'international' => ['+7 (999) 123-45-67', '+79991234567'],
            'russian eight' => ['8 999 123 45 67', '+79991234567'],
            'russian seven' => ['7-999-123-45-67', '+79991234567'],
            'russian ten digits' => ['9991234567', '+79991234567'],
            'other country' => ['+1 (202) 555-0123', '+12025550123'],
        ];
    }

    public function testExtractsUniquePhonesWithoutTreatingDatesAsPhones(): void
    {
        $text = 'Позвоните +7 (999) 123-45-67 или 8 999 123 45 67 до 08.10.2026 в 15:30.';

        self::assertSame(['+79991234567'], SupportPhoneNumber::extract($text));
    }

    public function testDoesNotRecognizeShortNumbersAndIdentifiers(): void
    {
        self::assertSame([], SupportPhoneNumber::extract('Заказ 12345678, код 4821, дата 2026-10-08.'));
    }

    public function testNormalizesPhonesInsideTelegramText(): void
    {
        self::assertSame(
            'Наберите +79991234567, пожалуйста.',
            SupportPhoneNumber::normalizeInText('Наберите 8 (999) 123-45-67, пожалуйста.'),
        );
    }
}
