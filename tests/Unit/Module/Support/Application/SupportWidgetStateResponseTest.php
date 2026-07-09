<?php

namespace tests\Unit\Module\Support\Application;

use app\Modules\Support\Application\Dto\SupportWidgetStateResponse;
use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Domain\SupportSettings;
use PHPUnit\Framework\TestCase;

final class SupportWidgetStateResponseTest extends TestCase
{
    public function testExposesSupportSettingsForWidgetFrontend(): void
    {
        $response = new SupportWidgetStateResponse(
            new SupportSettings(
                publicKey: 10,
                title: 'Задать вопрос',
                welcomeMessage: 'Привет',
                offlineMessage: 'Мы офлайн',
                timezone: 'Europe/Moscow',
                workingHours: 'Пн-Пт 10:00-19:00',
                askName: true,
                askEmail: true,
                askPhone: false,
                autoReply: 'Спасибо',
            ),
            SupportPlanLimit::free(),
            3,
            7,
        );

        $data = $response->toArray();

        self::assertSame('Задать вопрос', $data['title']);
        self::assertSame('Europe/Moscow', $data['timezone']);
        self::assertSame('Пн-Пт 10:00-19:00', $data['working_hours']);
        self::assertSame([
            'ask_name' => true,
            'ask_email' => true,
            'ask_phone' => false,
        ], $data['visitor_form']);
        self::assertSame('Спасибо', $data['auto_reply']);
        self::assertSame(3, $data['limits']['used_conversations']);
        self::assertSame(7, $data['limits']['used_messages']);
        self::assertSame(0, $data['limits']['used_operator_replies_today']);
        self::assertSame(10, $data['limits']['operator_replies_per_day']);
    }

    public function testHidesPhoneFieldWhenDailyReplyLimitIsExhausted(): void
    {
        $response = new SupportWidgetStateResponse(
            new SupportSettings(10, askPhone: true),
            SupportPlanLimit::free(),
            0,
            0,
            10,
        );

        $data = $response->toArray();

        self::assertFalse($data['visitor_form']['ask_phone']);
    }
}
