<?php

namespace tests\Integration\Module\Support;

use app\Modules\Support\Domain\SupportSettings;
use app\Modules\Support\Infrastructure\YiiSupportSettingsRepository;
use tests\Integration\Support\YiiIntegrationTestCase;

final class YiiSupportSettingsRepositoryTest extends YiiIntegrationTestCase
{
    public function testSavesNotificationChannels(): void
    {
        $publicKey = 990301;
        $this->createClient($publicKey);

        $repository = new YiiSupportSettingsRepository();

        self::assertTrue($repository->save(new SupportSettings(
            publicKey: $publicKey,
            notifyEmail: true,
            notifyTelegram: true,
            telegramBotToken: 'telegram-token',
            telegramChatId: 'telegram-chat',
            notifyMax: true,
            maxApiUrl: 'https://max.example/api',
            maxBotToken: 'max-token',
            maxChatId: 'max-chat',
            notificationEmails: 'manager@example.ru, support@example.ru',
            workSchedule: [
                'mode' => 'everyday',
                'round_the_clock' => false,
                'days' => [
                    'mon' => ['enabled' => true, 'from' => '10:00', 'to' => '19:00'],
                ],
            ],
            holidaySchedule: [
                ['date' => '2026-01-01', 'closed' => true, 'from' => '', 'to' => ''],
            ],
        )));

        $settings = $repository->getForClient($publicKey);

        self::assertTrue($settings->notifyEmail);
        self::assertSame('manager@example.ru, support@example.ru', $settings->notificationEmails);
        self::assertTrue($settings->notifyTelegram);
        self::assertSame('telegram-token', $settings->telegramBotToken);
        self::assertSame('telegram-chat', $settings->telegramChatId);
        self::assertTrue($settings->notifyMax);
        self::assertSame('https://max.example/api', $settings->maxApiUrl);
        self::assertSame('max-token', $settings->maxBotToken);
        self::assertSame('max-chat', $settings->maxChatId);
        self::assertSame('everyday', $settings->normalizedWorkSchedule()['mode']);
        self::assertSame('2026-01-01', $settings->holidaySchedule[0]['date']);
    }
}
