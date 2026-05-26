<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class LogsApiOpen extends ActiveRecord
{
    public static function tableName()
    {
        return 'logs_api_open';
    }

    public static function queryInsertLog(mixed $public_key, string $day_log, string $user_id, string $roles_data)
    {
        $query = "INSERT INTO logs_api_open (public_key, date_day, json_users, json_roles_data, count_all,count_unic) 
                    VALUES('$public_key', '$day_log','$user_id','$roles_data',1,1)";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function queryUpdateLog($public_key, $day_log, $json_users, $json_roles,$count_unic)
    {
        $query = "UPDATE logs_api_open SET json_users='$json_users', json_roles_data='$json_roles', count_all = (count_all + 1),count_unic=$count_unic WHERE public_key='$public_key' and date_day = '$day_log'";
        return Yii::$app->db->createCommand($query)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['public_key','date_day'], 'required'],
            [['public_key', 'count_all','count_unic'], 'integer'],
        ];
    }
    public static function primaryKey()
    {
        return ['public_key', 'date_day'];
    }

    public static function getDataCount(string $type_count,array $publicKeys,string $startDate,string $endDate)
    {

        $type_count="logs_api_open.date_day,logs_api_open.count_$type_count as value,logs_api_open.public_key,users.firm";
        $query = self::find()
            ->select($type_count)
            ->innerJoin('users','users.id = logs_api_open.public_key')
            ->where(['logs_api_open.public_key' => $publicKeys])
            ->andWhere( ['and', "logs_api_open.date_day <= '$endDate'", "logs_api_open.date_day >= '$startDate'"])
            ->andWhere(['not', ['users.firm' => null]])->orderBy('date_day');

        return $query;
        //echo $query->createCommand()->getRawSql();exit;

    }
}