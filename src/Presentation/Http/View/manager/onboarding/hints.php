<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintRecord;
use yii\helpers\Html;

/**
 * @var OnboardingHintRecord[] $hints
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Подсказки';
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin">
        <h3 class="uk-margin-remove">Подсказки</h3>
        <?= Html::a('Создать подсказку', ['/manager/onboarding/hint-create', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary']) ?>
    </div>

    <div class="sw-instruction-grid">
        <?php foreach ($hints as $hint): ?>
            <article class="sw-instruction-card">
                <div class="sw-instruction-card__top">
                    <h4><?= Html::encode($hint->title) ?></h4>
                    <span class="uk-label <?= $hint->is_active ? '' : 'uk-label-warning' ?>"><?= $hint->is_active ? 'включена' : 'выключена' ?></span>
                </div>
                <div><?= Html::encode(mb_substr(strip_tags($hint->content), 0, 160)) ?></div>
                <div class="uk-text-meta"><?= Html::encode($hint->selector) ?> · <?= $hint->standalone_enabled ? 'самостоятельная' : 'для сценариев' ?></div>
                <div class="sw-instruction-actions uk-margin-small-top">
                    <?= Html::a('Редактировать', ['/manager/onboarding/hint-update', 'id' => $hint->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                    <?= Html::a('Удалить', ['/manager/onboarding/hint-delete', 'id' => $hint->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small', 'data' => ['method' => 'post', 'confirm' => 'Удалить подсказку?']]) ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
