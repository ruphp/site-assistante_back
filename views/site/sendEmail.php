<?php

use yii\helpers\Html;
use ruwmapps\yii2_uikit3\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserSendEmailForm */
/* @var $form ActiveForm */
?>
<div class="main-sendEmail uk-container uk-position-relative">

    <?php  $form = ActiveForm::begin(['options' => [ 'class' => 'uk-form-stacked']]);?>

    <?= $form->field($model, 'email', ['options' => ['class' => 'uk-margin']])->input('string', ['class' => 'uk-input uk-form-width-large']); ?>

    <div class="form-group">
        <?=Html::submitButton('Отправить', ['class' => 'uk-button uk-button-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- main-sendEmail -->