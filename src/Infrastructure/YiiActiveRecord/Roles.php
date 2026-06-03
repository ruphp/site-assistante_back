<?php

namespace app\Infrastructure\YiiActiveRecord;

use Yii;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property int $public_key
 * @property string $name
 * @property double $id_role_in_system
 */
class Roles extends \yii\db\ActiveRecord
{
    public static function getSgRole($role)
    {
        return static::find()
            ->select('id')
            ->where(['public_key' => Yii::$app->user->identity->getPublicKey(),'id_role_in_system' => $role])
            ->cache(3600)
            ->asArray()->one();
    }
    public static function getRoles($public_key)
    {
        return static::find()->where(['public_key' => $public_key])->cache(3600)->all();
    }

    public static function getSgRoleName($role)
    {
        return static::find()
            ->select('name')
            ->where(['public_key' => Yii::$app->user->identity->getPublicKey(),'id' => $role])->cache(3600)->asArray()->one();
    }

    public static function getSystemRoleName($role)
    {
        return static::find()
            ->select('name')
            ->where(['public_key' => Yii::$app->user->identity->getPublicKey(),'id_role_in_system' => $role])->cache(3600)->asArray()->one();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['public_key', 'name', 'id_role_in_system'], 'required'],
            [['public_key'], 'integer'],
            [['id_role_in_system'], 'number'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'public_key' => 'public_key',
            'name' => 'Название роли',
            'id_role_in_system' => 'Идентификатор роли на Вашем сайте',
        ];
    }


}
