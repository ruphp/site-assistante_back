<?php

namespace app\Modules\Support\Infrastructure;

use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Support\Application\Contract\SupportManagerRecipientRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportManagerRecipient;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportProjectRecord;

final class YiiSupportManagerRecipientRepository implements SupportManagerRecipientRepositoryInterface
{
    public function listForClient(int $publicKey): array
    {
        $ownerPublicKey = (int)(SupportProjectRecord::find()
            ->select('owner_public_key')
            ->where(['public_key' => $publicKey, 'enabled' => 1])
            ->scalar() ?: $publicKey);

        $rows = Users::find()
            ->select(['users.id', 'users.name', 'users.email'])
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where([
                'users.public_key' => $ownerPublicKey,
                'users.status' => 1,
                'auth_assignment.item_name' => 'manager',
            ])
            ->orderBy(['users.id' => SORT_ASC])
            ->asArray()
            ->all();

        return array_map(
            static fn(array $row): SupportManagerRecipient => new SupportManagerRecipient(
                id: (int)$row['id'],
                name: (string)$row['name'],
                email: (string)$row['email'],
            ),
            $rows,
        );
    }
}
