<?php

namespace app\Infrastructure\YiiActiveRecord;

use yii\db\Expression;

final class LogQueryHelper
{
    public static function countColumn(string $typeCount): string
    {
        return $typeCount === 'unic' ? 'count_unic' : 'count_all';
    }

    public static function roleFilter($role): ?Expression
    {
        if (!$role) {
            return null;
        }

        return new Expression('json_roles_data ??| array[:role]', [
            ':role' => (string)$role,
        ]);
    }
}
