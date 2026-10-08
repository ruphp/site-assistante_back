<?php

namespace tests\Integration\Module\SmsDelivery;

use app\Infrastructure\User\UserIdentity;
use app\Modules\SmsDelivery\Application\SmsDeliveryService;
use app\Modules\Support\Application\Contract\SupportPushDeviceRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportPushNotificationSenderInterface;
use app\Modules\Support\Application\Service\SupportOperatorProjectAccessService;
use tests\Integration\Support\YiiIntegrationTestCase;

final class SmsDeliveryServiceTest extends YiiIntegrationTestCase
{
    public function testTaskIsIdempotentAndCanBeOpenedByManager(): void
    {
        $devices = new class implements SupportPushDeviceRepositoryInterface {
            public function upsertForUser(UserIdentity $user, string $token, string $platform): void {}
            public function deactivateByToken(string $token): void {}
            public function activeTokensForClient(int $publicKey): array { return []; }
            public function activeTokensForUser(int $userId): array { return []; }
            public function activeTokensForUsers(array $userIds): array { return ['device-token']; }
        };
        $sender = new class implements SupportPushNotificationSenderInterface {
            public array $sent = [];
            public function isConfigured(): bool { return true; }
            public function sendToToken(string $token, string $title, string $body, array $data = []): void
            {
                $this->sent[] = compact('token', 'title', 'body', 'data');
            }
        };
        $service = new SmsDeliveryService($devices, $sender, new SupportOperatorProjectAccessService());
        $credentials = $service->rotateToken(2);
        $payload = [
            'requestId' => 'test-login-42',
            'phone' => '8 (999) 123-45-67',
            'text' => 'Код подтверждения: 1234',
            'expiresAt' => (new \DateTimeImmutable('+5 minutes'))->format(DATE_ATOM),
        ];

        $created = $service->createTask($credentials['token'], $payload, '203.0.113.10');
        $duplicate = $service->createTask($credentials['token'], $payload, '203.0.113.10');

        self::assertSame($created['id'], $duplicate['id']);
        self::assertSame(SmsDeliveryService::STATUS_NOTIFIED, $created['status']);
        self::assertSame('+79991234567', $created['phone']);
        self::assertCount(1, $sender->sent);
        self::assertCount(1, $service->pendingTasks([2]));

        $opened = $service->markSmsOpened((int)$created['id'], 2, [2]);
        self::assertSame(SmsDeliveryService::STATUS_SMS_OPENED, $opened['status']);
        self::assertSame([], $service->pendingTasks([2]));
    }
}
