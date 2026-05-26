<?php

namespace app\models;

use Yii;

/**
 *
 * @property int $id
 * @property string $uuid
 * @property int $public_key
 */
class UserUuid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_uuid';
    }

    public static function getUser($public_key, $uuid)
    {// связь му моделями
        return static::find()->select('*')->where([
            'uuid'         => $uuid,
            'public_key' => $public_key,
        ])->cache(3600)->one();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'public_key'], 'required'],
            [['public_key'], 'integer'],
            [['uuid'], 'string'],
            [['uuid', 'public_key'], 'unique', 'targetAttribute' => ['uuid', 'public_key']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'public_key' => 'public_key',
            'uuid'       => 'uuid',
        ];
    }
}
