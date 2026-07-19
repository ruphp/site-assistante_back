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
?>
<div class="auth-page uk-container uk-container-xsmall">
    <div class="uk-flex uk-flex-center">
        <div class="uk-card uk-card-default uk-card-body auth-card">
            <div class="auth-brand">
                <span class="auth-brand__name">SiteWidget</span>
                <span class="auth-brand__text">Панель управления виджетом для сайта</span>
            </div>
            <h2 class="auth-title">Вход в панель управления</h2>

            <?php
            $oauthClients = Yii::$app->authClientCollection->clients;
            app\Presentation\Yii\Asset\AppAsset::register($this);
            $form = ActiveForm::begin(['id' => 'user-login-form', 'classForm' => 'uk-form-stacked']); ?>
            <?= $form->field($userLoginForm, 'email')->label('Адрес электронной почты') ?>
            <?= $form->field($userLoginForm, 'password')->passwordInput()->label('Пароль') ?>
            <?= $form->field($userLoginForm, 'remember')->checkbox(['label' => 'Запомнить меня']) ?>
            <div class="auth-actions">
                <?= Html::submitButton('Войти', ['class' => 'uk-button uk-button-primary auth-button']) ?>
                <a href="/join" class="uk-button uk-button-default auth-button auth-button-secondary">Зарегистрироваться</a>
            </div>
            <?php ActiveForm::end(); ?>
            <?php if (isset($oauthClients['yandex']) || isset($oauthClients['vkontakte'])): ?>
                <hr class="auth-divider">
                <div class="uk-grid-small auth-socials" uk-grid>
                    <?php if (isset($oauthClients['yandex'])): ?>
                        <div><?= Html::a('Войти через Яндекс ID', ['/site/auth', 'authclient' => 'yandex'], ['class' => 'uk-button uk-button-default auth-button auth-button-secondary']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($oauthClients['vkontakte'])): ?>
                        <div><?= Html::a('Войти через VK', ['/site/auth', 'authclient' => 'vkontakte'], ['class' => 'uk-button uk-button-default auth-button auth-button-secondary']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="auth-footer">
                <?= Html::a('Забыли пароль?', ['/send-email'], ['class' => 'auth-link']) ?>
            </div>
        </div>
    </div>
</div>
