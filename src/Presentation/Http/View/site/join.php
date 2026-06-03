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
            <?= Html::submitButton('Создать',
                ['class' => 'uk-button uk-button-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


