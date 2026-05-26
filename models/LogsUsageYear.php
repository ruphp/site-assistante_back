<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

class LogsUsageYear extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'year_usage_logs';
    }

    public static function count_all($public_key, $type)
    {
        //debug(1,1);
        $query = "select sum(count_all) count_all
                from year_usage_logs
                where type = '$type' and public_key='$public_key' group by type";
        return static::findBySql($query)->asArray()->one();
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


    public static function queryInsertLog($first_day, $public_key, $type,  $json_users, $json_roles_data)
    {

        $query = "INSERT INTO year_usage_logs (first_day, public_key, type,  json_users, json_roles_data,count_all, count_unic) 
                    VALUES('$first_day', '$public_key', '$type',  '$json_users', '$json_roles_data',1,1)";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function queryUpdateLog( $first_day,$public_key,$type, $json_users, $json_roles_data,$count_unic)
    {
        $query = "UPDATE year_usage_logs SET json_users='$json_users', json_roles_data='$json_roles_data', count_all = (count_all + 1),count_unic=$count_unic WHERE public_key='$public_key' and type='$type' and first_day = '$first_day'";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function getData(string $type_count, $public_key,  array $first_day,$role,$type)
    {

        $and_role = $role ? "json_roles_data ??| array['$role']":'';
        $type_count="first_day,sum(count_$type_count) as value";

        $query = self::find()
            ->select($type_count)
            ->where(['public_key' => $public_key,'first_day' => $first_day])
            ->andWhere( ["type"=> $type])
            ->andWhere(new Expression($and_role))
            ->groupBy("first_day")
            ->orderBy('first_day');
        return $query;
    }
}