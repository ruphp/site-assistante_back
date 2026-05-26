<?php

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

/** @var object $userJoinForm*/

$this->title = 'Создание нового клиента';

?>
<div class="uk-container uk-container-xsmall">
    <div>
        <div class="uk-card uk-card-large uk-card-default uk-card-body">
            <h2 class="bd-title">Создание нового клиента</h2>
            <?php


            app\assets\AppAsset::register($this);
            $form = ActiveForm::begin(['id' => 'user-join-form', 'classForm' => 'uk-form-stacked']);
            ?>
            <?= $form->field($userJoinForm, 'firm')?>
            <?= $form->field($userJoinForm, 'name')?>

            <?php

            if($_ENV['TYPE_AUTH'] == 'RSAA' ) {
                echo $form->field($userJoinForm, 'email')->hiddenInput(['value'=> strtolower(substr(md5(time()),0,10))."@local.local"])->label('');
                echo $form->field($userJoinForm, 'password')->hiddenInput(['value'=> 12345])->label('');
                echo $form->field($userJoinForm, 'password2')->hiddenInput(['value'=> 12345])->label('');
            }else{
                echo $form->field($userJoinForm, 'email')->label('Адрес электронной почты');
                echo $form->field($userJoinForm, 'password')->passwordInput()->label('Пароль');
                echo $form->field($userJoinForm, 'password2')->passwordInput()->label('Повторите пароль');
            }




            echo Html::submitButton('Создать',['class' => 'uk-button uk-button-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


