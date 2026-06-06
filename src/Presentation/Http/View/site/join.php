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
<div class="uk-container uk-container-xsmall">
    <div>
        <div class="uk-card uk-card-large uk-card-default uk-card-body">
            <h2 class="bd-title">Регистрация</h2>
            <?php
            app\Presentation\Yii\Asset\AppAsset::register($this);
            $form = ActiveForm::begin(['id' => 'user-join-form', 'classForm' => 'uk-form-stacked']); ?>
            <?= $form->field($userJoinForm, 'name')->label('Имя') ?>
            <?= $form->field($userJoinForm, 'email')->label('Адрес электронной почты') ?>
            <?= $form->field($userJoinForm, 'firm')->label('Компания') ?>
            <?= $form->field($userJoinForm, 'password')->passwordInput()->label('Пароль') ?>
            <?= $form->field($userJoinForm, 'password2')->passwordInput()->label('Повторите пароль') ?>
            <?php if ($captchaEnabled): ?>
                <?php $this->registerJsFile('https://smartcaptcha.yandexcloud.net/captcha.js', ['defer' => true]); ?>
                <div class="uk-margin">
                    <div class="smart-captcha" data-sitekey="<?= Html::encode($captchaSiteKey) ?>"></div>
                </div>
            <?php endif; ?>
            <?= Html::submitButton('Создать',
                ['class' => 'uk-button uk-button-primary']) ?>
            <?php ActiveForm::end(); ?>
            <?php if (isset($oauthClients['yandex']) || isset($oauthClients['vkontakte'])): ?>
                <hr>
                <div class="uk-grid-small" uk-grid>
                    <?php if (isset($oauthClients['yandex'])): ?>
                        <div><?= Html::a('Создать через Яндекс ID', ['/site/auth', 'authclient' => 'yandex'], ['class' => 'uk-button uk-button-default']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($oauthClients['vkontakte'])): ?>
                        <div><?= Html::a('Создать через VK', ['/site/auth', 'authclient' => 'vkontakte'], ['class' => 'uk-button uk-button-default']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


