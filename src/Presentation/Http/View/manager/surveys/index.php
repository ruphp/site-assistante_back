<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyFormRecord;
use app\Presentation\Http\View\Helper\RussianPlural;
use yii\helpers\Html;

/**
 * @var SurveyFormRecord[] $surveys
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 * @var int $activeCount
 * @var int $delayedCount
 * @var int $completedCount
 */

$this->title = 'Анкетирование';
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin">
        <div>
            <h3 class="uk-margin-remove">Анкетирование</h3>
            <div class="uk-text-meta">Анкеты показываются посетителям в модальном окне, а отложенные остаются в виджете.</div>
        </div>
        <?= Html::a('Создать анкету', ['/manager/surveys/create', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary']) ?>
    </div>

    <div class="sw-module-summary sw-survey-summary">
        <div class="sw-module-stat"><b><?= Html::encode((string)count($surveys)) ?></b><span><?= Html::encode(RussianPlural::word(count($surveys), 'анкета', 'анкеты', 'анкет')) ?></span></div>
        <div class="sw-module-stat"><b><?= Html::encode((string)$activeCount) ?></b><span><?= Html::encode(RussianPlural::word((int)$activeCount, 'включена', 'включены', 'включены')) ?></span></div>
        <div class="sw-module-stat"><b><?= Html::encode((string)$completedCount) ?></b><span><?= Html::encode(RussianPlural::word((int)$completedCount, 'ответ', 'ответа', 'ответов')) ?></span></div>
        <div class="sw-module-stat"><b><?= Html::encode((string)$delayedCount) ?></b><span><?= Html::encode(RussianPlural::word((int)$delayedCount, 'отложена', 'отложены', 'отложены')) ?></span></div>
    </div>

    <div class="sw-instruction-grid">
        <?php foreach ($surveys as $survey): ?>
            <article class="sw-instruction-card">
                <div class="sw-instruction-card__top">
                    <h4><?= Html::encode($survey->title) ?></h4>
                    <span class="uk-label <?= $survey->is_active ? '' : 'uk-label-warning' ?>"><?= $survey->is_active ? 'включена' : 'выключена' ?></span>
                </div>
                <div class="uk-text-meta">
                    <?= $survey->is_important ? 'Важная · ' : '' ?>Порядок <?= Html::encode((string)$survey->sort_order) ?>
                    <?php if ($survey->date_start || $survey->date_finish): ?>
                        · <?= Html::encode((string)($survey->date_start ?: '...')) ?> - <?= Html::encode((string)($survey->date_finish ?: '...')) ?>
                    <?php endif; ?>
                </div>
                <div class="sw-instruction-actions uk-margin-small-top">
                    <?= Html::a('Редактировать', ['/manager/surveys/update', 'id' => $survey->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                    <?= Html::a('Удалить', ['/manager/surveys/delete', 'id' => $survey->id, 'projectId' => $activeProject->id], [
                        'class' => 'uk-button uk-button-default uk-button-small',
                        'data' => ['method' => 'post', 'confirm' => 'Удалить анкету и ответы по ней?'],
                    ]) ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
