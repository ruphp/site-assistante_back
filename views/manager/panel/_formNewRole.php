<?php

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;


//echo 'Создать';
app\assets\AppAsset::register($this);
$form3 = ActiveForm::begin(); ?>
<?= $form3->field($newrole, 'name')->input('string', ['class' => 'uk-input uk-form-width-large', 'value' => '']) ?>
<?= $form3->field($newrole, 'id_role_in_system')->input('string', ['class' => 'uk-input uk-form-width-large', 'value' => '']) ?>
<?= $form3->field($newrole, 'public_key')->hiddenInput(['value' => Yii::$app->user->identity->getPublicKey()])->label('') ?>
<button class="uk-button uk-button-default uk-modal-close" type="button">Отменить</button>
<?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>
<?php ActiveForm::end() ?>
