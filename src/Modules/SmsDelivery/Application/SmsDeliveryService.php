<?php

namespace app\Modules\SmsDelivery\Application;

use app\Modules\SmsDelivery\Infrastructure\YiiActiveRecord\SmsDeliveryEventRecord;
use app\Modules\SmsDelivery\Infrastructure\YiiActiveRecord\SmsDeliveryProjectRecord;
use app\Modules\SmsDelivery\Infrastructure\YiiActiveRecord\SmsDeliveryTaskRecord;
use app\Modules\Support\Application\Contract\SupportPushDeviceRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportPushNotificationSenderInterface;
use app\Modules\Support\Application\Service\SupportOperatorProjectAccessService;
use app\Modules\Support\Domain\SupportPhoneNumber;
use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportProjectRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportSettingsRecord;
use Yii;
use yii\httpclient\Client;

final class SmsDeliveryService
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_NOTIFIED = 'notified';
    public const STATUS_SMS_OPENED = 'sms_opened';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_FAILED = 'failed';

    public function __construct(
        private readonly SupportPushDeviceRepositoryInterface $pushDevices,
        private readonly SupportPushNotificationSenderInterface $pushSender,
        private readonly SupportOperatorProjectAccessService $projectAccess,
    ) {
    }

    public function projectSettings(int $publicKey): array
    {
        $record = SmsDeliveryProjectRecord::findOne(['public_key' => $publicKey]);

        return [
            'configured' => $record instanceof SmsDeliveryProjectRecord,
            'enabled' => $record !== null && (int)$record->enabled === 1,
            'tokenPrefix' => $record === null ? '' : (string)$record->api_token_prefix,
            'callbackUrl' => $record === null ? '' : (string)$record->callback_url,
            'callbackSecret' => $record === null ? '' : (string)$record->callback_secret,
            'counts' => $this->statusCounts($publicKey),
        ];
    }

    public function rotateToken(int $publicKey): array
    {
        $this->assertPlanAvailable($publicKey);
        $token = 'sw_sms_' . Yii::$app->security->generateRandomString(48);
        $record = SmsDeliveryProjectRecord::findOne(['public_key' => $publicKey]) ?? new SmsDeliveryProjectRecord();
        $record->public_key = $publicKey;
        $record->api_token_hash = hash('sha256', $token);
        $record->api_token_prefix = substr($token, 0, 15);
        $record->callback_secret = (string)($record->callback_secret ?: Yii::$app->security->generateRandomString(48));
        $record->enabled = 1;
        $record->created_at = $record->created_at ?: date('Y-m-d H:i:s');
        $record->updated_at = date('Y-m-d H:i:s');
        $record->save(false);

        return ['token' => $token, 'callbackSecret' => (string)$record->callback_secret];
    }

    public function saveSettings(int $publicKey, string $callbackUrl, bool $enabled): void
    {
        $record = SmsDeliveryProjectRecord::findOne(['public_key' => $publicKey]);
        if (!$record instanceof SmsDeliveryProjectRecord) {
            throw new \RuntimeException('Сначала создайте токен проекта');
        }

        $callbackUrl = trim($callbackUrl);
        if ($callbackUrl !== '' && !$this->validCallbackUrl($callbackUrl)) {
            throw new \InvalidArgumentException('Callback должен быть публичным HTTPS-адресом');
        }

        $record->callback_url = $callbackUrl !== '' ? $callbackUrl : null;
        $record->enabled = $enabled ? 1 : 0;
        $record->updated_at = date('Y-m-d H:i:s');
        $record->save(false);
    }

    public function createTask(string $token, array $payload, string $remoteAddress): array
    {
        $project = $this->projectByToken($token);
        $publicKey = (int)$project->public_key;
        $this->assertPlanAvailable($publicKey);

        $requestId = trim((string)($payload['requestId'] ?? $payload['request_id'] ?? ''));
        if ($requestId === '' || strlen($requestId) > 128 || !preg_match('/^[A-Za-z0-9._:-]+$/', $requestId)) {
            throw new \InvalidArgumentException('Некорректный requestId');
        }

        $existing = SmsDeliveryTaskRecord::findOne(['public_key' => $publicKey, 'request_id' => $requestId]);
        if ($existing instanceof SmsDeliveryTaskRecord) {
            return $this->taskData($existing);
        }

        $phone = SupportPhoneNumber::normalize((string)($payload['phone'] ?? ''));
        if ($phone === null) {
            throw new \InvalidArgumentException('Некорректный номер телефона');
        }

        $text = trim((string)($payload['text'] ?? $payload['smsText'] ?? ''));
        if ($text === '' || mb_strlen($text) > 480) {
            throw new \InvalidArgumentException('Текст SMS обязателен и не должен превышать 480 символов');
        }

        $expiresAt = $this->expiresAt($payload['expiresAt'] ?? $payload['expires_at'] ?? null);
        $this->assertRateLimits($publicKey, $phone, $remoteAddress);

        $task = new SmsDeliveryTaskRecord();
        $task->public_key = $publicKey;
        $task->request_id = $requestId;
        $task->phone = $phone;
        $task->sms_text = $text;
        $task->status = self::STATUS_QUEUED;
        $task->expires_at = $expiresAt->format('Y-m-d H:i:s');
        $task->request_ip_hash = $remoteAddress === '' ? null : hash('sha256', $publicKey . ':' . $remoteAddress);
        $task->created_at = date('Y-m-d H:i:s');
        $task->updated_at = date('Y-m-d H:i:s');
        $task->save(false);
        $this->event((int)$task->id, self::STATUS_QUEUED);

        $tokens = $this->pushDevices->activeTokensForUsers(
            $this->projectAccess->operatorIdsForProject($publicKey),
        );
        $projectName = $this->projectName($publicKey);
        if ($this->pushSender->isConfigured()) {
            foreach ($tokens as $deviceToken) {
                $this->pushSender->sendToToken(
                    $deviceToken,
                    'Нужно отправить SMS-код',
                    $projectName . ': ' . $phone,
                    [
                        'type' => 'sms.delivery',
                        'sms_task_id' => (string)$task->id,
                        'title' => 'Нужно отправить SMS-код',
                        'body' => $projectName . ': ' . $phone,
                    ],
                );
            }
        }

        if ($tokens !== [] && $this->pushSender->isConfigured()) {
            $task->status = self::STATUS_NOTIFIED;
            $task->updated_at = date('Y-m-d H:i:s');
            $task->save(false, ['status', 'updated_at']);
            $this->event((int)$task->id, self::STATUS_NOTIFIED, ['devices' => count($tokens)]);
        }

        $this->sendCallback($project, $task);
        return $this->taskData($task);
    }

    public function taskStatus(string $token, string $requestId): array
    {
        $project = $this->projectByToken($token);
        $this->expireTasks([(int)$project->public_key]);
        $task = SmsDeliveryTaskRecord::findOne([
            'public_key' => (int)$project->public_key,
            'request_id' => $requestId,
        ]);
        if (!$task instanceof SmsDeliveryTaskRecord) {
            throw new \RuntimeException('Задание не найдено');
        }

        return $this->taskData($task);
    }

    public function pendingTasks(array $publicKeys): array
    {
        $publicKeys = array_values(array_unique(array_filter(array_map('intval', $publicKeys))));
        if ($publicKeys === []) {
            return [];
        }

        $this->expireTasks($publicKeys);
        return array_map(
            fn(SmsDeliveryTaskRecord $task): array => $this->taskData($task),
            SmsDeliveryTaskRecord::find()
                ->where(['public_key' => $publicKeys, 'status' => [self::STATUS_QUEUED, self::STATUS_NOTIFIED]])
                ->andWhere(['>', 'expires_at', date('Y-m-d H:i:s')])
                ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
                ->limit(100)
                ->all(),
        );
    }

    public function markSmsOpened(int $taskId, int $userId, array $publicKeys): array
    {
        $task = SmsDeliveryTaskRecord::find()
            ->where(['id' => $taskId, 'public_key' => array_map('intval', $publicKeys)])
            ->one();
        if (!$task instanceof SmsDeliveryTaskRecord) {
            throw new \RuntimeException('Задание не найдено');
        }
        if (strtotime((string)$task->expires_at) <= time()) {
            $this->expireTasks([(int)$task->public_key]);
            throw new \RuntimeException('Срок задания истёк');
        }

        if ((string)$task->status !== self::STATUS_SMS_OPENED) {
            $task->status = self::STATUS_SMS_OPENED;
            $task->opened_at = date('Y-m-d H:i:s');
            $task->opened_by_user_id = $userId;
            $task->updated_at = date('Y-m-d H:i:s');
            $task->save(false, ['status', 'opened_at', 'opened_by_user_id', 'updated_at']);
            $this->event($taskId, self::STATUS_SMS_OPENED, ['userId' => $userId]);

            $project = SmsDeliveryProjectRecord::findOne(['public_key' => (int)$task->public_key]);
            if ($project instanceof SmsDeliveryProjectRecord) {
                $this->sendCallback($project, $task);
            }
        }

        return $this->taskData($task);
    }

    private function projectByToken(string $token): SmsDeliveryProjectRecord
    {
        $token = trim(preg_replace('/^Bearer\s+/i', '', $token) ?? '');
        $project = $token === '' ? null : SmsDeliveryProjectRecord::findOne([
            'api_token_hash' => hash('sha256', $token),
            'enabled' => 1,
        ]);
        if (!$project instanceof SmsDeliveryProjectRecord) {
            throw new \DomainException('Токен проекта недействителен');
        }
        return $project;
    }

    private function assertPlanAvailable(int $publicKey): void
    {
        $ownerPublicKey = (int)(SupportProjectRecord::find()->select('owner_public_key')->where([
            'public_key' => $publicKey,
            'enabled' => 1,
        ])->scalar() ?: $publicKey);
        $plan = (string)(SupportSettingsRecord::find()->select('plan')->where(['public_key' => $ownerPublicKey])->scalar() ?: SupportPlan::FREE);
        if (SupportPlan::normalize($plan) === SupportPlan::FREE) {
            throw new \DomainException('SMS-коды доступны на тарифе Start или Pro');
        }
    }

    private function expiresAt(mixed $value): \DateTimeImmutable
    {
        try {
            $expiresAt = trim((string)$value) === '' ? new \DateTimeImmutable('+5 minutes') : new \DateTimeImmutable((string)$value);
        } catch (\Throwable) {
            throw new \InvalidArgumentException('Некорректный expiresAt');
        }
        $now = new \DateTimeImmutable();
        if ($expiresAt <= $now || $expiresAt > $now->modify('+30 minutes')) {
            throw new \InvalidArgumentException('expiresAt должен быть в пределах ближайших 30 минут');
        }
        return $expiresAt;
    }

    private function assertRateLimits(int $publicKey, string $phone, string $remoteAddress): void
    {
        $tenMinutesAgo = date('Y-m-d H:i:s', time() - 600);
        if ((int)SmsDeliveryTaskRecord::find()->where(['public_key' => $publicKey, 'phone' => $phone])->andWhere(['>=', 'created_at', $tenMinutesAgo])->count() >= 5) {
            throw new \OverflowException('Слишком много запросов для этого номера');
        }
        if ($remoteAddress !== '') {
            $hash = hash('sha256', $publicKey . ':' . $remoteAddress);
            if ((int)SmsDeliveryTaskRecord::find()->where(['public_key' => $publicKey, 'request_ip_hash' => $hash])->andWhere(['>=', 'created_at', $tenMinutesAgo])->count() >= 30) {
                throw new \OverflowException('Слишком много запросов с этого адреса');
            }
        }
    }

    private function expireTasks(array $publicKeys): void
    {
        $tasks = SmsDeliveryTaskRecord::find()->where([
            'public_key' => $publicKeys,
            'status' => [self::STATUS_QUEUED, self::STATUS_NOTIFIED],
        ])->andWhere(['<=', 'expires_at', date('Y-m-d H:i:s')])->all();
        if ($tasks === []) {
            return;
        }

        foreach ($tasks as $task) {
            $task->status = self::STATUS_EXPIRED;
            $task->updated_at = date('Y-m-d H:i:s');
            $task->save(false, ['status', 'updated_at']);
            $this->event((int)$task->id, self::STATUS_EXPIRED);
            $project = SmsDeliveryProjectRecord::findOne(['public_key' => (int)$task->public_key]);
            if ($project instanceof SmsDeliveryProjectRecord) {
                $this->sendCallback($project, $task);
            }
        }
    }

    private function taskData(SmsDeliveryTaskRecord $task): array
    {
        return [
            'id' => (int)$task->id,
            'requestId' => (string)$task->request_id,
            'projectPublicKey' => (int)$task->public_key,
            'projectName' => $this->projectName((int)$task->public_key),
            'phone' => (string)$task->phone,
            'text' => (string)$task->sms_text,
            'status' => (string)$task->status,
            'expiresAt' => (string)$task->expires_at,
            'createdAt' => (string)$task->created_at,
        ];
    }

    private function projectName(int $publicKey): string
    {
        return (string)(SupportProjectRecord::find()->select('name')->where(['public_key' => $publicKey, 'enabled' => 1])->scalar() ?: ('Проект ' . $publicKey));
    }

    private function event(int $taskId, string $status, array $details = []): void
    {
        $event = new SmsDeliveryEventRecord();
        $event->task_id = $taskId;
        $event->status = $status;
        $event->details = $details === [] ? null : json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $event->created_at = date('Y-m-d H:i:s');
        $event->save(false);
    }

    private function statusCounts(int $publicKey): array
    {
        $rows = SmsDeliveryTaskRecord::find()->select(['status', 'count' => new \yii\db\Expression('COUNT(*)')])->where(['public_key' => $publicKey])->groupBy('status')->asArray()->all();
        $counts = [];
        foreach ($rows as $row) {
            $counts[(string)$row['status']] = (int)$row['count'];
        }
        return $counts;
    }

    private function validCallbackUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower((string)($parts['host'] ?? ''));
        if (($parts['scheme'] ?? '') !== 'https' || $host === '' || $host === 'localhost' || str_ends_with($host, '.local')) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
        }

        return filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }

    private function sendCallback(SmsDeliveryProjectRecord $project, SmsDeliveryTaskRecord $task): void
    {
        $url = trim((string)$project->callback_url);
        if ($url === '') {
            return;
        }
        $payload = json_encode([
            'requestId' => (string)$task->request_id,
            'status' => (string)$task->status,
            'occurredAt' => date(DATE_ATOM),
        ], JSON_UNESCAPED_SLASHES);
        try {
            (new Client())->createRequest()->setMethod('POST')->setUrl($url)->addHeaders([
                'Content-Type' => 'application/json',
                'X-SiteWidget-Signature' => hash_hmac('sha256', $payload, (string)$project->callback_secret),
            ])->setContent($payload)->setOptions(['timeout' => 3])->send();
        } catch (\Throwable $exception) {
            Yii::warning($exception->getMessage(), 'sms-delivery-callback');
        }
    }
}
