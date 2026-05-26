<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

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
        $query = "INSERT INTO week_usage_logs (monday_day, sunday_day, public_key, type,  json_users, json_roles_data, count_all,count_unic) 
                    VALUES('$monday_day', '$sunday_day', '$public_key', '$type', '$json_users', '$json_roles_data',1,1)";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function queryUpdateLog($monday_day, $sunday_day, $public_key, $type, $json_users, $json_roles_data,$count_unic)
    {
        $query = "UPDATE week_usage_logs SET json_users='$json_users', json_roles_data='$json_roles_data', count_all = (count_all + 1),count_unic=$count_unic 
                             WHERE public_key='$public_key' and type='$type'  AND monday_day='$monday_day' AND sunday_day='$sunday_day'";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function getData(string $type_count, $public_key,  array $categories_dates_monday,$role,$type)
    {

        $and_role = $role ? "json_roles_data ??| array['$role']":'';
        $type_count="monday_day,sunday_day,sum(count_$type_count) as value";

        $query = self::find()
            ->select($type_count)
            ->where(['public_key' => $public_key,'monday_day' => $categories_dates_monday])
            ->andWhere(new Expression($and_role))
            ->andWhere( ["type"=> $type])
            ->groupBy("monday_day,sunday_day")
            ->orderBy('monday_day');
        return $query;
    }

}