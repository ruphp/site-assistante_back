<?php
/**
 * User  - юзер (кто)
 * Identity- идентификация (какой процесс)
 * относится к базе по умолчанию
 */

namespace app\Infrastructure\User;

use app\Infrastructure\YiiActiveRecord\Params;
use app\Infrastructure\YiiActiveRecord\Roles;
use app\Infrastructure\YiiActiveRecord\Users;
use yii\web\IdentityInterface;

class UserIdentity extends Users implements IdentityInterface
{

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]); //найти одного
    }
    public static function findName($name)
    {
        return static::findOne(['name' => $name]);
    }
    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findName($token);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPublicKey()
    {
        return $this->public_key;
    }

    public function getFirm()
    {
        return $this->firm;
    }



    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // проверим ключ
        return $this->getAuthKey() === $authKey; // сравниваем текущий с тем что пришел
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // какой ключ используется
        return mb_strtolower($this->email); // пока емайл
    }

    // связи му моделями

    public function getParams()
    {
        return $this->hasOne(Params::className(), ['public_key' => 'public_key']);
    }
    public function getRoles()
    {// связь му моделями
        return $this->hasMany(Roles::className(), ['public_key' => 'public_key']);
    }
    public function getListUsersByPk()
    {
        return self::find()
            ->select('*')
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['users.public_key' => $this->public_key])->andWhere(['<>','users.id',$this->id])->asArray()->all();
    }
}
