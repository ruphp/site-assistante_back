<?php

namespace app\Infrastructure\User;

use app\Application\User\Contract\UserAccountServiceInterface;
use app\Infrastructure\YiiActiveRecord\Users;
use Yii;
use yii\helpers\Html;

final class YiiUserAccountService implements UserAccountServiceInterface
{
    public function sendJoinNotifications(
        string $name,
        string $email,
        string $password,
        string $adminEmail,
        string $userSubject,
        string $adminSubject,
        string $adminBody
    ): bool {
        if (YII_ENV_DEV) {
            return true;
        }

        Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([$adminEmail => 'SiteWidget'])
            ->setSubject($userSubject)
            ->setTextBody('тест')
            ->setHtmlBody($this->joinUserBody($name, $email, $password))
            ->send();

        Yii::$app->mailer->compose()
            ->setTo($adminEmail)
            ->setFrom([$adminEmail => 'SiteWidget'])
            ->setSubject($adminSubject)
            ->setTextBody($adminBody . ' ' . $name . ' ' . $email)
            ->send();

        return true;
    }

    public function sendPasswordResetEmail(string $email): bool
    {
        $user = Users::findOne(['status' => 1, 'email' => $email]);

        if ($user === null) {
            return false;
        }

        $link = Html::a(
            'Для смены пароля перейдите по этой ссылке.',
            Yii::$app->urlManager->createAbsoluteUrl([
                '/user/reset-password',
                'key' => $user->passhash,
                'user' => $user->id,
            ])
        );

        return Yii::$app->mailer->compose()
            ->setTo($user->email)
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Сброс пароля для ' . $user->name)
            ->setHtmlBody($this->passwordResetBody($user->name, $link))
            ->send();
    }

    public function canResetPassword(string $key, int $userId): bool
    {
        return Users::validatePasswordEmail($key, $userId) !== null;
    }

    public function resetPasswordByToken(string $key, int $userId, string $password): bool
    {
        $user = Users::validatePasswordEmail($key, $userId);

        if ($user === null) {
            return false;
        }

        $user->setPassword($password);

        return $user->save();
    }

    private function joinUserBody($name, $email, $pass): string
    {
        return "
<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
    <title>Cпасибо за регистрацию на sitewidget.ru!</title>
</head>
<body>
<p>Добрый день, $name! Большое спасибо, что присоединились к SiteWidget.</p>
<p></p>
<p></p>
<p>С чего начать?
<br>Первый шаг:
<br>Авторизуйтесь на сайте для доступа в консоль администратора, используя ваши логин и пароль
<br>Логин: $email
<br>Пароль: указанный при регистрации
<br>
<br>Второй шаг:
<br>В административной панели в разделе «Настройки подключения» введите домен вашего сайта. Сформировавшийся скрипт скопируйте и разместите его на сайте, на нужных страницах перед закрывающим тегом ‹/body›
<br>
<br>Готово! Теперь вы можете:
<br>• Размещать контент для формирования базы знаний
<br>• Привлекать пользователей с помощью персонализированного взаимодействия с web-ресурсом
<br>• Ускорять процесс внедрения новых функций и адаптации сотрудников
</p>
<p>
-- <br>
Команда SiteWidget</p>
</body>
</html> ";
    }

    private function passwordResetBody($name, $link): string
    {
        return "
<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
    <title>Смена пароля на sitewidget.ru!</title>
</head>
<body>
<p>Добрый день, $name !</p>
<p> $link </p>
-- <br>
Команда SiteWidget</p>
</body>
</html> ";
    }
}
