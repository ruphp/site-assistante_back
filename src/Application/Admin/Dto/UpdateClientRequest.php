<?php

namespace app\Application\Admin\Dto;

final class UpdateClientRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $firm,
        public readonly string $name,
        public readonly string $email,
        public readonly int $status,
        public readonly int $gmt,
        public readonly bool $changePassword,
        public readonly array $modules,
    ) {
    }

    public static function fromPost(int $id, array $post): self
    {
        $user = $post['Users'] ?? [];

        return new self(
            $id,
            (string)($user['firm'] ?? ''),
            (string)($user['name'] ?? ''),
            mb_strtolower((string)($user['email'] ?? '')),
            (int)($user['status'] ?? 0),
            (int)($user['gmt'] ?? 0),
            (bool)($user['change_password'] ?? false),
            self::normalizeModules((array)($user['modules'] ?? [])),
        );
    }

    private static function normalizeModules(array $modules): array
    {
        if (empty($modules['chatbots'])) {
            $modules['bigdata'] = 0;
        }

        return $modules;
    }
}
