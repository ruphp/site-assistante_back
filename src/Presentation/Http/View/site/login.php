<?php
/**
 * Представление страницы логирования
 */

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

$this->title = 'Авторизация пользователей';
$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Личный кабинет пользователя SiteWidget',
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'SiteWidget личный кабинет пользователя, авторизация в SiteWidget',
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
                $oauthClients = Yii::$app->authClientCollection->clients;
                app\Presentation\Yii\Asset\AppAsset::register($this);
                $form = ActiveForm::begin(['id' => 'user-login-form', 'classForm' => 'uk-form-stacked']); ?>
                <?= $form->field($userLoginForm, 'email')->label('Адрес электронной почты') ?>
                <?= $form->field($userLoginForm, 'password')->passwordInput()->label('Пароль') ?>
                <?= $form->field($userLoginForm, 'remember')->checkbox(['label' => 'Запомнить меня']) ?>
                <?= Html::submitButton('Войти',
                    ['class' => 'uk-button uk-button-primary']) ?>
                <a href="/join" class="uk-button uk-button-default">Зарегистрироваться</a>
                <?php ActiveForm::end(); ?>
                <?php if (isset($oauthClients['yandex']) || isset($oauthClients['vkontakte'])): ?>
                    <hr>
                    <div class="uk-grid-small" uk-grid>
                        <?php if (isset($oauthClients['yandex'])): ?>
                            <div><?= Html::a('Войти через Яндекс ID', ['/site/auth', 'authclient' => 'yandex'], ['class' => 'uk-button uk-button-default']) ?></div>
                        <?php endif; ?>
                        <?php if (isset($oauthClients['vkontakte'])): ?>
                            <div><?= Html::a('Войти через VK', ['/site/auth', 'authclient' => 'vkontakte'], ['class' => 'uk-button uk-button-default']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php echo Html::a('Забыли пароль?', ['/send-email']);
            }

            ?>
        </div>
    </div>
</div>
