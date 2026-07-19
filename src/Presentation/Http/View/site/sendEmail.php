<?php

use yii\helpers\Html;
use ruwmapps\yii2_uikit3\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\Presentation\Http\Form\UserSendEmailForm */
/* @var $form ActiveForm */
?>
<div class="auth-page uk-container uk-container-xsmall">
    <div class="uk-flex uk-flex-center">
        <div class="uk-card uk-card-default uk-card-body auth-card">
            <h2 class="auth-title">Восстановление доступа</h2>

            <?php  $form = ActiveForm::begin(['options' => [ 'class' => 'uk-form-stacked']]);?>

            <?= $form->field($model, 'email', ['options' => ['class' => 'uk-margin']])->input('string', ['class' => 'uk-input uk-form-width-large']); ?>

            <div class="auth-actions">
                <?=Html::submitButton('Отправить', ['class' => 'uk-button uk-button-primary auth-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div><!-- main-sendEmail -->
