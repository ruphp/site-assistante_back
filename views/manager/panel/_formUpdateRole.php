<?php

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

app\assets\AppAsset::register($this);
$form2 = ActiveForm::begin(); ?>
<?= $form2->field($role, 'name')->input('string', ['class' => 'uk-input uk-form-width-large']); ?>
<?= $form2->field($role, 'id_role_in_system')->input('string', ['class' => 'uk-input uk-form-width-large']); ?>
<?= $form2->field($role, 'id')->hiddenInput(['value' => $role['id']])->label(''); ?>
<button class="uk-button uk-button-default uk-modal-close" type="button">Отменить</button>
<?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']); ?>
<?php ActiveForm::end();

?>
