<?php
/**
 * Представление страницы логирования
 */

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

$this->title = 'Авторизация пользователей';
$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Личный кабинет пользователя в центре интерактивной поддержки пользователей SMARTIUS GUIDE',
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'SMARTIUS GUIDE личный кабинет пользователя, авторизация в SMARTIUS GUIDE',
]);
$urlencode = urlencode($_ENV['RSAA_REDIRECT_URI']);
$href = "{$_ENV['RSAA_AUTH_URL']}?client_id={$_ENV['RSAA_CLIENT']}&scope=openid&response_type=code&redirect_uri=$urlencode";
?>
<div class="uk-container uk-container-xsmall">
    <div>
        <div class="uk-card uk-card-large uk-card-default uk-card-body">
            <h2 class="bd-title">Вход</h2>

            <?php
            if ($_ENV['TYPE_DEPLOYED'] == 'MIRS') {
                ?>
                <a href="<?php echo $href ?>" class="uk-button uk-button-default">Авторизоваться через РСАА</a>
                <?php
            } else {
                app\assets\AppAsset::register($this);
                $form = ActiveForm::begin(['id' => 'user-login-form', 'classForm' => 'uk-form-stacked']); ?>
                <?= $form->field($userLoginForm, 'email')->label('Адрес электронной почты') ?>
                <?= $form->field($userLoginForm, 'password')->passwordInput()->label('Пароль') ?>
                <?= $form->field($userLoginForm, 'remember')->checkbox(['label' => 'Запомнить меня']) ?>
                <?= Html::submitButton('Войти',
                    ['class' => 'uk-button uk-button-primary']) ?>
                <a href="/join" class="uk-button uk-button-default">Зарегистрироваться</a>
                <?php ActiveForm::end(); ?>
                <?php echo Html::a('Забыли пароль?', ['/send-email']);
            }

            ?>
        </div>
    </div>
</div>