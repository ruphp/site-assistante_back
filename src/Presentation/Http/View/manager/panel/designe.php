<?php

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

/**
 * @var $params
 */

$this->title = 'Оформление';
?>

<div class="uk-container uk-position-relative">
<?php
app\Presentation\Yii\Asset\AppAsset::register($this);
$form = ActiveForm::begin(['options' => ['id' => 'testForm', 'class' => 'uk-form-stacked']]);

echo $form->field($params, 'leftbutton')->radioList([
    0 => 'По правому краю',
    1 => 'По левому краю',
]);

echo Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']);
ActiveForm::end();
?>
</div>
