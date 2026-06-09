<?php

namespace app\Infrastructure\YiiActiveRecord;

use Yii;
use yii\db\ActiveRecord;

class LogsUsageWeek extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'week_usage_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['public_key','monday_day','sunday_day', 'type'], 'required'],
            [['public_key', 'count_all', 'count_unic'], 'integer'],
            [['type'], 'string'],
        ];
    }

    public static function primaryKey(): array
    {
        return ['public_key','monday_day','sunday_day', 'type'];
    }


    public static function queryInsertLog($monday_day, $sunday_day, $public_key, $type, $json_users, $json_roles_data )
    {
        return Yii::$app->db->createCommand(
            'INSERT INTO week_usage_logs (monday_day, sunday_day, public_key, type, json_users, json_roles_data, count_all, count_unic)
                VALUES (:monday_day, :sunday_day, :public_key, :type, CAST(:json_users AS jsonb), CAST(:json_roles_data AS jsonb), 1, 1)',
            [
                ':monday_day' => $monday_day,
                ':sunday_day' => $sunday_day,
                ':public_key' => $public_key,
                ':type' => $type,
                ':json_users' => $json_users,
                ':json_roles_data' => $json_roles_data,
            ]
        )->execute();
    }

    public static function queryUpdateLog($monday_day, $sunday_day, $public_key, $type, $json_users, $json_roles_data,$count_unic)
    {
        return Yii::$app->db->createCommand(
            'UPDATE week_usage_logs
                SET json_users = CAST(:json_users AS jsonb),
                    json_roles_data = CAST(:json_roles_data AS jsonb),
                    count_all = (count_all + 1),
                    count_unic = :count_unic
                WHERE public_key = :public_key AND type = :type AND monday_day = :monday_day AND sunday_day = :sunday_day',
            [
                ':json_users' => $json_users,
                ':json_roles_data' => $json_roles_data,
                ':count_unic' => $count_unic,
                ':public_key' => $public_key,
                ':type' => $type,
                ':monday_day' => $monday_day,
                ':sunday_day' => $sunday_day,
            ]
        )->execute();
    }

    public static function getData(string $type_count, $public_key,  array $categories_dates_monday,$role,$type)
    {

        $countColumn = LogQueryHelper::countColumn($type_count);
        $type_count="monday_day,sunday_day,sum($countColumn) as value";

        $query = self::find()
            ->select($type_count)
            ->where(['public_key' => $public_key,'monday_day' => $categories_dates_monday])
            ->andWhere( ["type"=> $type])
            ->groupBy("monday_day,sunday_day")
            ->orderBy('monday_day');

        if ($roleFilter = LogQueryHelper::roleFilter($role)) {
            $query->andWhere($roleFilter);
        }

        return $query;
    }

}
