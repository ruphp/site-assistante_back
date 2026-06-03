<?php

namespace app\Application\Cron;

final class CronLogAccumulator
{
    public function initialUserJson($userId): string
    {
        return json_encode([0 => $userId], JSON_FORCE_OBJECT);
    }

    public function initialRolesJson(array $roles): string
    {
        return json_encode(array_fill_keys(array_values($roles), 1), JSON_FORCE_OBJECT);
    }

    public function mergeUsers(object $oldLog, $userId): array
    {
        $users = $oldLog->json_users;
        $users[] = $userId;
        $uniqueUsers = array_unique($users);

        return [
            json_encode($uniqueUsers, JSON_FORCE_OBJECT),
            count($uniqueUsers),
        ];
    }

    public function mergeUsersAndRoles(object $oldLog, $userId, array $rolesData): array
    {
        [$usersJson, $uniqueCount] = $this->mergeUsers($oldLog, $userId);
        $roles = $oldLog->json_roles_data;

        foreach ($rolesData as $role) {
            if (isset($roles[$role])) {
                $roles[$role] += 1;
            } else {
                $roles[$role] = 1;
            }
        }

        return [
            $usersJson,
            json_encode($roles, JSON_FORCE_OBJECT),
            $uniqueCount,
        ];
    }
}
