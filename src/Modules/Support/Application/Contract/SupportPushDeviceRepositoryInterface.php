<?php

namespace app\Modules\Support\Application\Contract;

use app\Infrastructure\User\UserIdentity;

interface SupportPushDeviceRepositoryInterface
{
    public function upsertForUser(UserIdentity $user, string $token, string $platform): void;

    public function deactivateByToken(string $token): void;

    /**
     * @return string[]
     */
    public function activeTokensForClient(int $publicKey): array;

    /**
     * @return string[]
     */
    public function activeTokensForUser(int $userId): array;

    /**
     * @param int[] $userIds
     * @return string[]
     */
    public function activeTokensForUsers(array $userIds): array;
}
