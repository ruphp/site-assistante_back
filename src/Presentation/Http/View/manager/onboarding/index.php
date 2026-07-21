<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use yii\helpers\Html;

/**
 * @var OnboardingRecord[] $onboardings
 * @var int $hintsCount
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Онбординг';
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin">
        <div>
            <h3 class="uk-margin-remove">Онбординг</h3>
            <div class="uk-text-meta">Сценарии онбординга и подсказки к элементам сайта. Подсказки могут работать самостоятельно или как шаг сценария.</div>
        </div>
        <?= Html::a('Создать сценарий', ['/manager/onboarding/create', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary']) ?>
    </div>

    <div class="sw-onboarding-summary uk-grid-small uk-child-width-1-3@m" uk-grid>
        <div><div class="sw-onboarding-stat"><b><?= Html::encode((string)count($onboardings)) ?></b><span>сценариев</span></div></div>
        <div><div class="sw-onboarding-stat"><b><?= Html::encode((string)$hintsCount) ?></b><span>подсказок</span></div></div>
        <div><div class="sw-onboarding-stat"><b><?= Html::encode((string)$activeProject->publicKey) ?></b><span>public key</span></div></div>
    </div>

    <div class="sw-instruction-grid sw-onboarding-list">
        <?php foreach ($onboardings as $onboarding): ?>
            <article class="sw-instruction-card">
                <div class="sw-instruction-card__top">
                    <h4><?= Html::encode($onboarding->title) ?></h4>
                    <span class="uk-label <?= $onboarding->is_active ? '' : 'uk-label-warning' ?>"><?= $onboarding->is_active ? 'включен' : 'выключен' ?></span>
                </div>
                <div class="uk-text-meta">Пауза перед запуском: <?= Html::encode((string)$onboarding->timeout) ?> мс · порядок <?= Html::encode((string)$onboarding->sort_order) ?></div>
                <div class="sw-instruction-actions uk-margin-small-top">
                    <?= Html::a('Редактировать', ['/manager/onboarding/update', 'id' => $onboarding->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                    <?= Html::a('Разделы', ['/manager/onboarding/sections', 'onboardingId' => $onboarding->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                    <?= Html::a('Удалить', ['/manager/onboarding/delete', 'id' => $onboarding->id, 'projectId' => $activeProject->id], [
                        'class' => 'uk-button uk-button-default uk-button-small',
                        'data' => ['method' => 'post', 'confirm' => 'Удалить сценарий?'],
                    ]) ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
