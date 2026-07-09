<?php

use app\Application\Panel\Dto\ClientProjectView;
use yii\helpers\Html;

/**
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$path = '/' . trim(Yii::$app->request->pathInfo, '/');
if ($path === '/') {
    $path = '/manager';
}
?>

<div class="uk-margin">
    <ul class="uk-tab">
        <?php foreach ($projects as $project): ?>
            <li class="<?= $project->id === $activeProject->id ? 'uk-active' : '' ?>">
                <?= Html::a(Html::encode($project->name), $path . '?projectId=' . $project->id) ?>
            </li>
        <?php endforeach; ?>
        <li>
            <a href="#modal-project-create" uk-toggle>
                <span uk-icon="plus"></span>
            </a>
        </li>
    </ul>

    <div class="uk-text-meta uk-margin-small-top">
        Текущий проект:
        <strong><?= Html::encode($activeProject->name) ?></strong>
        <?php if ($activeProject->domain !== ''): ?>
            · <?= Html::encode($activeProject->domain) ?>
        <?php endif; ?>
    </div>
</div>

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
