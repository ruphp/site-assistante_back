<?php
/**
 * Представление страницы регистрации
 */

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

$this->title = 'Регистрация пользователей';
$this->registerMetaTag([
    'name'    => 'description',
    'content' => 'Регистрация пользователя SiteWidget',
]);
$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => 'SiteWidget регистрация пользователя, регистрация в SiteWidget',
]);
?>
<div class="auth-page uk-container uk-container-xsmall">
    <div class="uk-flex uk-flex-center">
        <div class="uk-card uk-card-default uk-card-body auth-card">
            <h2 class="auth-title">Регистрация</h2>
            <?php
            app\Presentation\Yii\Asset\AppAsset::register($this);
            $form = ActiveForm::begin(['id' => 'user-join-form', 'classForm' => 'uk-form-stacked']); ?>
            <?= $form->field($userJoinForm, 'name')->label('Ваше имя') ?>
            <?= $form->field($userJoinForm, 'email')->label('Email') ?>
            <?= $form->field($userJoinForm, 'password')->passwordInput()->label('Придумайте пароль') ?>
            <?= $form->field($userJoinForm, 'password2')->passwordInput()->label('Пароль ещё раз') ?>
            <?php if ($captchaEnabled): ?>
                <?php $this->registerJsFile('https://smartcaptcha.yandexcloud.net/captcha.js', ['defer' => true]); ?>
                <div class="uk-margin">
                    <div class="smart-captcha" data-sitekey="<?= Html::encode($captchaSiteKey) ?>"></div>
                </div>
            <?php endif; ?>
            <div class="auth-actions">
                <?= Html::submitButton('Создать аккаунт', ['class' => 'uk-button uk-button-primary auth-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
            <?php if (isset($oauthClients['yandex']) || isset($oauthClients['vkontakte'])): ?>
                <hr class="auth-divider">
                <div class="uk-grid-small auth-socials" uk-grid>
                    <?php if (isset($oauthClients['yandex'])): ?>
                        <div><?= Html::a('Создать через Яндекс ID', ['/site/auth', 'authclient' => 'yandex'], ['class' => 'uk-button uk-button-default auth-button auth-button-secondary']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($oauthClients['vkontakte'])): ?>
                        <div><?= Html::a('Создать через VK', ['/site/auth', 'authclient' => 'vkontakte'], ['class' => 'uk-button uk-button-default auth-button auth-button-secondary']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


