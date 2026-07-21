<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Infrastructure\YiiActiveRecord\Roles;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var OnboardingRecord $onboarding
 * @var array $structure
 * @var Roles[] $roles
 * @var int[] $selectedRoleIds
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = $onboarding->isNewRecord ? 'Создать сценарий' : 'Редактировать сценарий';
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', [
        'projects' => $projects,
        'activeProject' => $activeProject,
        'projectTabsPath' => '/manager/onboarding',
    ]) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3><?= Html::encode($this->title) ?></h3>
    <?= Html::beginForm('', 'post', ['class' => 'uk-form-stacked sw-form-panel']) ?>
        <div class="uk-margin">
            <?= Html::label('Название сценария', 'nav-title', ['class' => 'uk-form-label']) ?>
            <?= Html::input('text', 'Onboarding[title]', $onboarding->title, ['id' => 'nav-title', 'class' => 'uk-input', 'required' => true]) ?>
        </div>
        <div class="uk-grid-small uk-child-width-1-3@m" uk-grid>
            <div>
                <?= Html::label('Задержка запуска, мс', 'nav-timeout', ['class' => 'uk-form-label']) ?>
                <?= Html::input('number', 'Onboarding[timeout]', (string)$onboarding->timeout, ['id' => 'nav-timeout', 'class' => 'uk-input']) ?>
            </div>
            <div>
                <?= Html::label('Порядок', 'nav-order', ['class' => 'uk-form-label']) ?>
                <?= Html::input('number', 'Onboarding[sort_order]', (string)$onboarding->sort_order, ['id' => 'nav-order', 'class' => 'uk-input']) ?>
            </div>
            <div class="uk-flex uk-flex-middle uk-margin-top">
                <label><?= Html::checkbox('Onboarding[is_active]', (bool)$onboarding->is_active) ?> Включен</label>
            </div>
        </div>
        <div class="uk-margin">
            <label><?= Html::checkbox('Onboarding[is_blur]', (bool)$onboarding->is_blur) ?> Затемнять страницу вокруг активного элемента</label>
        </div>
        <div class="uk-margin">
            <label><?= Html::checkbox('Onboarding[auto_start]', (bool)$onboarding->auto_start) ?> Запускать автоматически, если посетитель ещё не проходил этот онбординг</label>
        </div>
        <?php if ($roles !== []): ?>
            <div class="uk-margin">
                <div class="uk-form-label">Показывать ролям</div>
                <?= Html::checkboxList('role_ids', $selectedRoleIds, ArrayHelper::map($roles, 'id', 'name'), ['separator' => '<br>']) ?>
                <div class="uk-text-meta">Если роли не выбраны, сценарий доступен всем посетителям.</div>
            </div>
        <?php endif; ?>
        <div class="uk-margin">
            <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>
            <?= Html::a('Отмена', ['/manager/onboarding', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default']) ?>
        </div>
    <?= Html::endForm() ?>

    <?php if (!$onboarding->isNewRecord): ?>
        <section class="sw-instruction-card uk-margin-top">
            <?= $this->render('_structure', compact('onboarding', 'structure', 'activeProject')) ?>
        </section>
    <?php endif; ?>
</div>
