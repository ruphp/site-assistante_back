<?php

namespace app\models;

use PDO;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

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
        $query = "INSERT INTO day_usage_logs (public_key, date_day, type, json_users, json_roles_data, count_all,count_unic) 
                    VALUES('$public_key', '$day_log','$type','$user_id','$roles_data',1,1)";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function queryUpdateLog($public_key, $day_log, $json_users, $json_roles,$count_unic,$type)
    {
        $query = "UPDATE day_usage_logs SET json_users='$json_users', json_roles_data='$json_roles', count_all = (count_all + 1),count_unic=$count_unic WHERE public_key='$public_key' and type='$type' and date_day = '$day_log'";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function getData(string $type_count, $public_key,  string $start_date, string $end_date,$role,$type)
    {
        $and_role = $role ? "json_roles_data ??| array['$role']":'';
        $type_count="date_day,sum(count_$type_count) as value";
        Yii::$app->db->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $query = self::find()
            ->select($type_count)
            ->where(['public_key' => $public_key,])
            ->andWhere( ['<=', "date_day", $end_date])
            ->andWhere( ['>=', "date_day", $start_date])
            ->andWhere( ["type"=> $type])
            ->andWhere(new Expression($and_role))
            ->groupBy("date_day")
            ->orderBy('date_day');
        return $query;
    }
}