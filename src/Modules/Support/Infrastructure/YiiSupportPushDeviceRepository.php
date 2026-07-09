<?php

namespace app\Modules\Support\Infrastructure;

use app\Infrastructure\User\UserIdentity;
use app\Modules\Support\Application\Contract\SupportPushDeviceRepositoryInterface;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportPushDeviceRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportProjectRecord;

final class YiiSupportPushDeviceRepository implements SupportPushDeviceRepositoryInterface
{
    public function upsertForUser(UserIdentity $user, string $token, string $platform): void
    {
        $token = trim($token);
        $platform = strtolower(trim($platform)) ?: 'android';

        if ($token === '') {
            return;
        }

        $record = SupportPushDeviceRecord::find()
            ->where(['token' => $token])
            ->one();

        if (!$record instanceof SupportPushDeviceRecord) {
            $record = new SupportPushDeviceRecord();
            $record->created_at = date('Y-m-d H:i:s');
        }

        $record->public_key = (int)$user->public_key;
        $record->user_id = (int)$user->id;
        $record->token = $token;
        $record->platform = $platform;
        $record->is_active = 1;
        $record->last_seen_at = date('Y-m-d H:i:s');
        $record->updated_at = date('Y-m-d H:i:s');
        $record->save(false);
    }

    public function deactivateByToken(string $token): void
    {
        $token = trim($token);
        if ($token === '') {
            return;
        }

        $record = SupportPushDeviceRecord::find()
            ->where(['token' => $token])
            ->one();

        if (!$record instanceof SupportPushDeviceRecord) {
            return;
        }

        $record->is_active = 0;
        $record->updated_at = date('Y-m-d H:i:s');
        $record->save(false);
    }

    public function activeTokensForClient(int $publicKey): array
    {
        $ownerPublicKey = (int)(SupportProjectRecord::find()
            ->select('owner_public_key')
            ->where(['public_key' => $publicKey, 'enabled' => 1])
            ->scalar() ?: $publicKey);

        $rows = SupportPushDeviceRecord::find()
            ->select(['token'])
            ->where([
                'public_key' => $ownerPublicKey,
                'is_active' => 1,
            ])
            ->andWhere(['not', ['token' => null]])
            ->asArray()
            ->all();

        $tokens = [];
        foreach ($rows as $row) {
            $token = trim((string)($row['token'] ?? ''));
            if ($token !== '') {
                $tokens[] = $token;
            }
        }

        return array_values(array_unique($tokens));
    }
}
