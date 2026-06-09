<?php

namespace app\Infrastructure\YiiActiveRecord;

use Yii;
use yii\db\ActiveRecord;

class LogsUsageMonth extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'month_usage_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['public_key','first_day', 'type'], 'required'],
            [['public_key', 'count_all', 'count_unic'], 'integer'],
            [['type'], 'string'],
        ];
    }

    public static function primaryKey(): array
    {
        return ['public_key','first_day', 'type'];
    }


    public static function queryInsertLog($first_day, $public_key, $type, $json_users, $json_roles_data)
    {
        return Yii::$app->db->createCommand(
            'INSERT INTO month_usage_logs (first_day, public_key, type, json_users, json_roles_data, count_all, count_unic)
                VALUES (:first_day, :public_key, :type, CAST(:json_users AS jsonb), CAST(:json_roles_data AS jsonb), 1, 1)',
            [
                ':first_day' => $first_day,
                ':public_key' => $public_key,
                ':type' => $type,
                ':json_users' => $json_users,
                ':json_roles_data' => $json_roles_data,
            ]
        )->execute();
    }

    public static function queryUpdateLog($first_day, $public_key, $type, $json_users, $json_roles_data , $count_unic)
    {
        return Yii::$app->db->createCommand(
            'UPDATE month_usage_logs
                SET json_users = CAST(:json_users AS jsonb),
                    json_roles_data = CAST(:json_roles_data AS jsonb),
                    count_all = (count_all + 1),
                    count_unic = :count_unic
                WHERE public_key = :public_key AND type = :type AND first_day = :first_day',
            [
                ':json_users' => $json_users,
                ':json_roles_data' => $json_roles_data,
                ':count_unic' => $count_unic,
                ':public_key' => $public_key,
                ':type' => $type,
                ':first_day' => $first_day,
            ]
        )->execute();
    }

    public static function getData(string $type_count, $public_key,  array $first_day,$role,$type)
    {

        $countColumn = LogQueryHelper::countColumn($type_count);
        $type_count="first_day,sum($countColumn) as value";

        $query = self::find()
            ->select($type_count)
            ->where(['public_key' => $public_key,'first_day' => $first_day])
            ->andWhere( ["type"=> $type])
            ->groupBy("first_day")
            ->orderBy('first_day');

        if ($roleFilter = LogQueryHelper::roleFilter($role)) {
            $query->andWhere($roleFilter);
        }

        return $query;
    }
}
