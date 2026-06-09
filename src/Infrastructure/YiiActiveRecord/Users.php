<?php

namespace app\Infrastructure\YiiActiveRecord;

use app\Presentation\Http\Form\UserJoinForm;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $firm
 * @property int|null $public_key
 * @property int $status
 * @property string $passhash
 */
class Users extends ActiveRecord
{
    public const STATUS_PENDING = 0;
    public const STATUS_ACTIVE = 1;
    public $support_plan;
    public int $change_password;
    public array $modules;
    public static function tableName(): string
    {
        return 'users';
    }

    public static function existsEmail($email): int
    {
        return static::find()->where(['email' => $email])->count();
    }

    public static function findUserByEmail($email): ?Users
    {
        return static::findOne(['email' => $email]);
    }

    public static function findByEmailConfirmToken(string $token): ?Users
    {
        return static::findOne([
            'email_confirm_token' => $token,
            'status' => self::STATUS_PENDING,
        ]);
    }

    public static function getUserDataById($id): ?Users
    {
        return static::findOne($id);
    }

    public static function getListUsersManager(string $select='*',array $where=[]): array
    {

        return self::find()
            ->select($select)
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(array_merge(['auth_assignment.item_name' => 'manager'],$where))->orderBy('id')
            ->asArray()->all();
    }



    /**
     * @throws \yii\base\Exception
     */
    public function setUserJoinForm(UserJoinForm $userJoinForm): void
    {

        //Yii::warning($userJoinForm->name,'test');

        $this->name = $userJoinForm->name;
        $this->email = mb_strtolower($userJoinForm->email);
        $this->firm = $userJoinForm->firm;
        $this->public_key = null;
        $this->setPassword($userJoinForm->password);
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @throws \yii\base\Exception
     */
    public function setManagerJoinForm(UserJoinForm $userJoinForm): void
    {

        //Yii::warning($userJoinForm->name,'test');

        $this->name = $userJoinForm->name;
        $this->email = mb_strtolower($userJoinForm->email);
        $this->firm = Yii::$app->user->identity->getFirm();
        $this->public_key = Yii::$app->user->identity->getPublicKey();
        $this->setPassword($userJoinForm->password);
        $this->status = self::STATUS_ACTIVE;
    }

    public function prepareEmailConfirmation(): void
    {
        $this->status = self::STATUS_PENDING;
        $this->email_confirmed_at = null;
        $this->email_confirm_token = Yii::$app->security->generateRandomString(64);
    }

    public function confirmEmail(): bool
    {
        $this->status = self::STATUS_ACTIVE;
        $this->email_confirmed_at = date('Y-m-d H:i:s');
        $this->email_confirm_token = null;

        return $this->save();
    }

    public function upAdminJoinForm(UserJoinForm $userJoinForm): void
    {

        $this->name = $userJoinForm->name;
        $this->email = mb_strtolower($userJoinForm->email);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function setPassword($password): void
    {
        $this->passhash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->passhash);
    }

    public static function validatePasswordEmail($password, $user): ?Users
    {
        return static::findOne([
            'passhash' => $password,
            'id' => $user,
        ]);
    }

    public function getRole(): int|null
    {
        foreach(Yii::$app->authManager->getRolesByUser($this->id) as $key => $val) {
            return $key;
        }
        return null;
    }



    /**
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes): void
    {
        if ($this->id > 0) {
            parent::afterSave($insert, $changedAttributes);
            if ($insert) {
                if (is_null($this->public_key)) {
                    $this->public_key = $this->id;
                    $this->save();
                }
                if (Yii::$app->has('session')) {
                    Yii::$app->session->setFlash('success', 'Аккаунт создан', false);
                }
            }
            else {
                if (Yii::$app->has('session')) {
                    Yii::$app->session->setFlash('success', 'Аккаунт изменен', false);
                }
            }

        }
    }

    public static function gen_password($length = 6): string

    {

        $chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';

        $size = strlen($chars) - 1;

        $password = '';

        while($length--) {

            $password .= $chars[random_int(0, $size)];

        }

        return $password;

    }
    public function attributeLabels()
    {
        if($_ENV['TYPE_AUTH'] == 'RSAA' ){
            return [
                'public_key'         => 'Порядковый номер ИС',
                'firm'         => 'Наименование ИС',
                'name'         => 'Наименование ИС латиницей (имя клиента в РСАА)',
            ];
        }
        return [
            'public_key'         => 'Идентификатор',
            'firm'         => 'Наименование организации',
            'name'         => 'Короткое наименование организации латиницей',
        ];

    }

}
