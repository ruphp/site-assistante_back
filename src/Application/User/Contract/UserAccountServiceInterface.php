<?php

namespace app\Application\User\Contract;

interface UserAccountServiceInterface
{
    public function sendJoinNotifications(
        string $name,
        string $email,
        string $password,
        string $adminEmail,
        string $userSubject,
        string $adminSubject,
        string $adminBody
    ): bool;

    public function sendPasswordResetEmail(string $email): bool;

    public function canResetPassword(string $key, int $userId): bool;

    public function resetPasswordByToken(string $key, int $userId, string $password): bool;
}
