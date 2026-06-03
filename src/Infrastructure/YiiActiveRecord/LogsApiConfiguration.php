<?php



namespace app\Infrastructure\YiiActiveRecord;

use Yii;
use yii\data\DataFilter;
use yii\db\ActiveRecord;

class LogsApiConfiguration extends ActiveRecord
{
    public static function tableName()
    {
        return 'logs_api_configuration';
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
    public static function queryUpdateLog($public_key, $day_log, $json_users,$count_unic)
    {
        return Yii::$app->db->createCommand(
            'UPDATE logs_api_configuration
                SET json_users = CAST(:json_users AS jsonb),
                    count_all = (count_all + 1),
                    count_unic = :count_unic
                WHERE public_key = :public_key AND date_day = :date_day',
            [
                ':json_users' => $json_users,
                ':count_unic' => $count_unic,
                ':public_key' => $public_key,
                ':date_day' => $day_log,
            ]
        )->execute();
    }

    public static function queryInsertLog($public_key, $day_log,$user_id)
    {
        return Yii::$app->db->createCommand(
            'INSERT INTO logs_api_configuration (public_key, date_day, json_users, count_all, count_unic)
                VALUES (:public_key, :date_day, CAST(:json_users AS jsonb), 1, 1)',
            [
                ':public_key' => $public_key,
                ':date_day' => $day_log,
                ':json_users' => $user_id,
            ]
        )->execute();
    }

    public static function getDataCount(string $type_count,array $publicKeys,string $startDate,string $endDate)
    {

        $countColumn = LogQueryHelper::countColumn($type_count);
        $type_count="logs_api_configuration.date_day,logs_api_configuration.$countColumn as value,logs_api_configuration.public_key,users.firm";
        $query = self::find()
            ->select($type_count)
            ->innerJoin('users','users.id = logs_api_configuration.public_key')
            ->where(['logs_api_configuration.public_key' => $publicKeys])
            ->andWhere(['<=', 'logs_api_configuration.date_day', $endDate])
            ->andWhere(['>=', 'logs_api_configuration.date_day', $startDate])
            ->andWhere(['not', ['users.firm' => null]])->orderBy('date_day');

        return $query;
        //echo $query->createCommand()->getRawSql();exit;

    }


}
