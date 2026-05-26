<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

class LogsUsageQuart extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'quart_usage_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['public_key','first_quart_day','last_quart_day','type'], 'required'],
            [['public_key', 'count_all', 'count_unic'], 'integer'],
            [['type'], 'string'],
        ];
    }

    public static function primaryKey(): array
    {
        return ['public_key','first_quart_day','last_quart_day','type'];
    }


    public static function queryInsertLog($first_quart_day, $last_quart_day, $public_key, $type, $json_users, $json_roles_data)
    {
        $query = "INSERT INTO quart_usage_logs (first_quart_day, last_quart_day, public_key, type, json_users, json_roles_data, count_all, count_unic) 
                    VALUES('$first_quart_day', '$last_quart_day', '$public_key', '$type', '$json_users', '$json_roles_data', 1,1)";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function queryUpdateLog($first_quart_day, $last_quart_day, $public_key, $type, $json_users, $json_roles_data,$count_unic)
    {
        $query = "UPDATE quart_usage_logs SET json_users='$json_users', json_roles_data='$json_roles_data', count_all = (count_all + 1),count_unic=$count_unic WHERE public_key='$public_key' and type='$type' and first_quart_day = '$first_quart_day' and last_quart_day = '$last_quart_day'";
        return Yii::$app->db->createCommand($query)->execute();
    }

    public static function getData(string $type_count, $public_key,  array $quart_first,$role,$type)
    {
        $and_role = $role ? "json_roles_data ??| array['$role']":'';
        $type_count="first_quart_day,sum(count_$type_count) as value";

        $query = self::find()
            ->select($type_count)
            ->where(['public_key' => $public_key,'first_quart_day' => $quart_first])
            ->andWhere( ["type"=> $type])
            ->andWhere(new Expression($and_role))
            ->groupBy("first_quart_day")
            ->orderBy('first_quart_day');
        return $query;
    }
}