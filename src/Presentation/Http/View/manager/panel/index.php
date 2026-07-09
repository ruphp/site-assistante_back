<?php

use app\Application\Panel\Dto\ClientProjectView;
use yii\helpers\Html;

use ruwmapps\yii2_uikit3\LinkPager;

/** @var $pages */
/** @var $users */
/** @var ClientProjectView[] $projects */
/** @var ClientProjectView $activeProject */

$this->title = "Панель управления виджетом";


?>

<div class="uk-container uk-margin">
    <h3>Панель управления виджетом</h3>

    <div class="uk-grid-small uk-child-width-1-3@m uk-child-width-1-2@s" uk-grid>
        <?php foreach ($projects as $project): ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-middle">
                        <h4 class="uk-margin-remove"><?= Html::encode($project->name) ?></h4>
                        <?php if ($project->isDefault): ?>
                            <span class="uk-label">Основной</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($project->domain !== ''): ?>
                        <div class="uk-text-meta uk-margin-small-top"><?= Html::encode($project->domain) ?></div>
                    <?php endif; ?>
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
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div>
            <a class="uk-card uk-card-default uk-card-body uk-display-block uk-text-center" href="#modal-project-create" uk-toggle>
                <span uk-icon="icon: plus; ratio: 2"></span>
                <div class="uk-margin-small-top">Создать проект</div>
            </a>
        </div>
    </div>

    <?= $this->render('_projectTabs', compact('projects', 'activeProject')) ?>
</div>
