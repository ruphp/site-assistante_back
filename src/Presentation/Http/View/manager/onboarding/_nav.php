<?php

use app\Application\Panel\Dto\ClientProjectView;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var ClientProjectView $activeProject
 */

$projectParam = ['projectId' => $activeProject->id];
$items = [
    ['label' => 'Сценарии', 'url' => ['/manager/onboarding'] + $projectParam],
    ['label' => 'Подсказки', 'url' => ['/manager/onboarding/hints'] + $projectParam],
    ['label' => 'Создать сценарий', 'url' => ['/manager/onboarding/create'] + $projectParam],
    ['label' => 'Создать подсказку', 'url' => ['/manager/onboarding/hint-create'] + $projectParam],
];
?>

<div class="sw-instruction-nav uk-margin">
    <?php foreach ($items as $item): ?>
        <?= Html::a(Html::encode($item['label']), Url::to($item['url']), ['class' => 'uk-button uk-button-default uk-button-small']) ?>
    <?php endforeach; ?>
</div>
