<?php

namespace tests\Unit\Module\Support\Presentation;

use app\Modules\Support\Presentation\Http\SupportPhoneLinkFormatter;
use PHPUnit\Framework\TestCase;

final class SupportPhoneLinkFormatterTest extends TestCase
{
    public function testFormatsPhoneAsCallLink(): void
    {
        $html = SupportPhoneLinkFormatter::phone('8 (999) 123-45-67');

        self::assertStringContainsString('href="tel:+79991234567"', $html);
        self::assertStringContainsString('8 (999) 123-45-67', $html);
    }

    public function testLinksPhonesInMessageAndEscapesOtherHtml(): void
    {
        $html = SupportPhoneLinkFormatter::message('<script>alert(1)</script> Позвоните +7 999 123-45-67');

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
        self::assertStringContainsString('href="tel:+79991234567"', $html);
    }

    public function testLeavesDatesAndCodesAsPlainText(): void
    {
        $html = SupportPhoneLinkFormatter::message('Код 1234 действует до 08.10.2026 15:30');

        self::assertStringNotContainsString('href="tel:', $html);
    }
}
