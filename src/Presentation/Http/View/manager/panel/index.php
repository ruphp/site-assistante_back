<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Support\Domain\SupportPlanLimit;
use yii\helpers\Html;

/** @var ClientProjectView[] $projects */
/** @var ClientProjectView $activeProject */

$this->title = "Проекты";
$supportPlan = SupportPlan::normalize((string)(Yii::$app->user->identity->support_plan ?? SupportPlan::FREE));
$projectLimit = SupportPlanLimit::forPlan($supportPlan);
$canCreateProjects = count($projects) < $projectLimit->maxProjects;


?>

<div class="uk-container uk-margin">
    <h3>Проекты</h3>

    <div class="uk-grid-small uk-child-width-1-3@m uk-child-width-1-2@s" uk-grid>
        <?php foreach ($projects as $project): ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-middle">
                        <h4 class="uk-margin-remove"><?= Html::encode($project->name) ?></h4>
                    </div>
                    <?php if ($project->domain !== ''): ?>
                        <div class="uk-text-meta uk-margin-small-top"><?= Html::encode($project->domain) ?></div>
                    <?php endif; ?>
                    <div class="uk-margin-small-top">
                        <span class="uk-text-meta">Public key:</span>
                        <code><?= $project->publicKey ?></code>
                    </div>
                    <div class="uk-margin-top uk-grid-small" uk-grid>
                        <div>
                            <?= Html::a('Параметры', '/manager/params?projectId=' . $project->id, ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                        </div>
                        <div>
                            <?= Html::a('Оформление', '/manager/designe?projectId=' . $project->id, ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        </div>
                        <div>
                            <?= Html::a('Поддержка', '/manager/support?projectId=' . $project->id, ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        </div>
                        <div>
                            <?= Html::a('Инструкции', '/manager/instructions?projectId=' . $project->id, ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        </div>
                        <div>
                            <?= Html::a('Онбординг', '/manager/onboarding?projectId=' . $project->id, ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        </div>
                        <div>
                            <?= Html::a('Анкеты', '/manager/surveys?projectId=' . $project->id, ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div>
            <a class="uk-card uk-card-default uk-card-body uk-display-block uk-text-center" href="<?= $canCreateProjects ? '#modal-project-create' : '#modal-project-limit' ?>" uk-toggle>
                <span uk-icon="icon: plus; ratio: 2"></span>
                <div class="uk-margin-small-top">Создать проект</div>
            </a>
        </div>
    </div>

</div>

<?php if ($canCreateProjects): ?>
    <div id="modal-project-create" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <h3 class="uk-modal-title">Новый проект</h3>
            <?= Html::beginForm('/manager/project-create', 'post', ['class' => 'uk-form-stacked']) ?>
                <div class="uk-margin">
                    <?= Html::label('Название проекта', 'project-name', ['class' => 'uk-form-label']) ?>
                    <?= Html::input('text', 'name', '', [
                        'id' => 'project-name',
                        'class' => 'uk-input',
                        'maxlength' => 255,
                        'placeholder' => 'Например: интернет-магазин',
                    ]) ?>
                </div>
                <div class="uk-margin">
                    <?= Html::label('Домен', 'project-domain', ['class' => 'uk-form-label']) ?>
                    <?= Html::input('text', 'domain', '', [
                        'id' => 'project-domain',
                        'class' => 'uk-input',
                        'maxlength' => 255,
                        'placeholder' => 'example.ru',
                    ]) ?>
                </div>
                <div class="uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Отмена</button>
                    <?= Html::submitButton('Создать', ['class' => 'uk-button uk-button-primary']) ?>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
<?php else: ?>
    <div id="modal-project-limit" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <h3 class="uk-modal-title">Дополнительные проекты</h3>
            <p>На текущем тарифе доступно проектов: <?= Html::encode((string)$projectLimit->maxProjects) ?>. Чтобы подключить несколько сайтов или тематик, перейдите на Pro-тариф.</p>
            <div class="uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close" type="button">Закрыть</button>
                <?= Html::a('Написать в виджет', '/', ['class' => 'uk-button uk-button-primary']) ?>
            </div>
        </div>
    </div>
<?php endif; ?>
