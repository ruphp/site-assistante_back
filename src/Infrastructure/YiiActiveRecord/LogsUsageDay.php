<?php

namespace app\Infrastructure\YiiActiveRecord;

use PDO;
use Yii;
use yii\db\ActiveRecord;

class LogsUsageDay extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'day_usage_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['public_key', 'date_day', 'type'], 'required'],
            [['public_key', 'count_all', 'count_unic'], 'integer'],
            [['type'], 'string'],
        ];
    }

    public static function primaryKey(): array
    {
        return ['public_key', 'date_day', 'type'];
    }


    public static function queryInsertLog(int $public_key, string $day_log, string $user_id, string $roles_data,$type)
    {
        return Yii::$app->db->createCommand(
            'INSERT INTO day_usage_logs (public_key, date_day, type, json_users, json_roles_data, count_all, count_unic)
                VALUES (:public_key, :date_day, :type, CAST(:json_users AS jsonb), CAST(:json_roles_data AS jsonb), 1, 1)',
            [
                ':public_key' => $public_key,
                ':date_day' => $day_log,
                ':type' => $type,
                ':json_users' => $user_id,
                ':json_roles_data' => $roles_data,
            ]
        )->execute();
    }

    public static function queryUpdateLog($public_key, $day_log, $json_users, $json_roles,$count_unic,$type)
    {
        return Yii::$app->db->createCommand(
            'UPDATE day_usage_logs
                SET json_users = CAST(:json_users AS jsonb),
                    json_roles_data = CAST(:json_roles_data AS jsonb),
                    count_all = (count_all + 1),
                    count_unic = :count_unic
                WHERE public_key = :public_key AND type = :type AND date_day = :date_day',
            [
                ':json_users' => $json_users,
                ':json_roles_data' => $json_roles,
                ':count_unic' => $count_unic,
                ':public_key' => $public_key,
                ':type' => $type,
                ':date_day' => $day_log,
            ]
        )->execute();
    }

    public static function getData(string $type_count, $public_key,  string $start_date, string $end_date,$role,$type)
    {
        $countColumn = LogQueryHelper::countColumn($type_count);
        $type_count="date_day,sum($countColumn) as value";
        Yii::$app->db->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $query = self::find()
            ->select($type_count)
            ->where(['public_key' => $public_key,])
            ->andWhere( ['<=', "date_day", $end_date])
            ->andWhere( ['>=', "date_day", $start_date])
            ->andWhere( ["type"=> $type])
            ->groupBy("date_day")
            ->orderBy('date_day');

        if ($roleFilter = LogQueryHelper::roleFilter($role)) {
            $query->andWhere($roleFilter);
        }

        return $query;
    }
}
