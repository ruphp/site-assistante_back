<?php

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

/**
 * @var $params
 * @var \app\Application\Panel\Dto\ClientProjectView[] $projects
 * @var \app\Application\Panel\Dto\ClientProjectView $activeProject
 */

$this->title = 'Оформление';
?>

<div class="uk-container uk-position-relative">
<?= $this->render('_projectTabs', compact('projects', 'activeProject')) ?>
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
