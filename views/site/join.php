<?php
/**
 * Представление страницы регистрации
 */

use Baha2Odeh\RecaptchaV3\RecaptchaV3Widget;
use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

$this->title = 'Регистрация пользователей';
$this->registerMetaTag([
    'name'    => 'description',
    'content' => 'Регистрация пользователя в центре интерактивной поддержки пользователей SMARTIUS GUIDE',
]);
$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => 'SMARTIUS GUIDE регистрация пользователя, регистрация в SMARTIUS GUIDE',
]);
?>
<div class="uk-container uk-container-xsmall">
    <div>
        <div class="uk-card uk-card-large uk-card-default uk-card-body">
            <h2 class="bd-title">Регистрация</h2>
            <?php
            app\assets\AppAsset::register($this);
            $form = ActiveForm::begin(['id' => 'user-join-form', 'classForm' => 'uk-form-stacked']); ?>
            <?= $form->field($userJoinForm, 'name')->label('Имя') ?>
            <?= $form->field($userJoinForm, 'email')->label('Адрес электронной почты') ?>
            <?= $form->field($userJoinForm, 'firm')->label('Компания') ?>
            <?= $form->field($userJoinForm, 'password')->passwordInput()->label('Пароль') ?>
            <?= $form->field($userJoinForm, 'password2')->passwordInput()->label('Повторите пароль') ?>
            <?php //echo $form->field($userJoinForm, 'code')->widget(RecaptchaV3Widget::className()); ?>
            <?= Html::submitButton('Создать',
                ['class' => 'uk-button uk-button-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


