<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSectionRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingStepRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var OnboardingRecord $onboarding
 * @var OnboardingSectionRecord $section
 * @var OnboardingStepRecord[] $steps
 * @var OnboardingStepRecord $editStep
 * @var OnboardingHintRecord[] $hints
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Шаги сценария';
$hintOptions = ['' => 'Без готовой подсказки'] + ArrayHelper::map($hints, 'id', 'title');
$positions = [0 => 'Сверху', 1 => 'Справа', 2 => 'Снизу', 3 => 'Слева'];
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', [
        'projects' => $projects,
        'activeProject' => $activeProject,
        'projectTabsPath' => '/manager/onboarding',
    ]) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3>Шаги: <?= Html::encode($onboarding->title) ?> · <?= Html::encode($section->title) ?></h3>
    <div class="uk-text-meta uk-margin-small-bottom">Шаг может взять готовую подсказку или хранить собственный текст и CSS-селектор.</div>

    <?php if ($editStep->isNewRecord): ?>
        <div class="sw-instruction-grid">
            <?php foreach ($steps as $step): ?>
                <article class="sw-instruction-card">
                    <div class="sw-instruction-card__top">
                        <h4>Шаг <?= Html::encode((string)$step->sort_order) ?></h4>
                        <span class="uk-label <?= $step->is_active ? '' : 'uk-label-warning' ?>"><?= $step->is_active ? 'включен' : 'выключен' ?></span>
                    </div>
                    <div><?= Html::encode(mb_substr(strip_tags($step->text), 0, 160)) ?></div>
                    <div class="uk-text-meta"><?= Html::encode($step->selector) ?></div>
                    <div class="sw-instruction-actions uk-margin-small-top">
                        <?= Html::a('Открыть шаг', ['/manager/onboarding/steps', 'sectionId' => $section->id, 'editId' => $step->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                        <?= Html::a('Удалить', ['/manager/onboarding/step-delete', 'id' => $step->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small', 'data' => ['method' => 'post', 'confirm' => 'Удалить шаг?']]) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h4 class="uk-margin-large-top"><?= $editStep->isNewRecord ? 'Новый шаг' : 'Редактировать шаг' ?></h4>
    <?= Html::beginForm('', 'post', ['class' => 'uk-form-stacked sw-form-panel']) ?>
        <?= $editStep->isNewRecord ? '' : Html::hiddenInput('OnboardingStep[id]', (string)$editStep->id) ?>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-2@m">
                <?= Html::label('Готовая подсказка', 'step-hint', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('OnboardingStep[hint_id]', $editStep->hint_id, $hintOptions, ['id' => 'step-hint', 'class' => 'uk-select']) ?>
            </div>
            <div class="uk-width-1-4@m">
                <?= Html::label('Позиция', 'step-position', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('OnboardingStep[position]', $editStep->position, $positions, ['id' => 'step-position', 'class' => 'uk-select']) ?>
            </div>
            <div class="uk-width-1-4@m">
                <?= Html::label('Порядок', 'step-order', ['class' => 'uk-form-label']) ?>
                <?= Html::input('number', 'OnboardingStep[sort_order]', (string)$editStep->sort_order, ['id' => 'step-order', 'class' => 'uk-input']) ?>
            </div>
        </div>
        <div class="uk-margin">
            <?= Html::label('CSS-селектор элемента', 'step-selector', ['class' => 'uk-form-label']) ?>
            <?= Html::input('text', 'OnboardingStep[selector]', (string)$editStep->selector, ['id' => 'step-selector', 'class' => 'uk-input', 'placeholder' => '#cart-button или .product-card:first-child']) ?>
            <?= $this->render('_selectorPicker', [
                'inputId' => 'step-selector',
                'defaultUrl' => $activeProject->domain,
                'target' => 'step',
            ]) ?>
        </div>
        <div class="uk-margin">
            <?= Html::label('Текст шага', 'step-text', ['class' => 'uk-form-label']) ?>
            <?= Html::textarea('OnboardingStep[text]', (string)$editStep->text, ['id' => 'step-text', 'class' => 'uk-textarea', 'rows' => 4]) ?>
        </div>
        <label><?= Html::checkbox('OnboardingStep[is_active]', (bool)$editStep->is_active) ?> Включен</label>
        <div class="uk-margin">
            <?= Html::submitButton($editStep->isNewRecord ? 'Добавить шаг' : 'Сохранить шаг', ['class' => 'uk-button uk-button-primary']) ?>
            <?php if (!$editStep->isNewRecord): ?>
                <?= Html::a('Отмена', ['/manager/onboarding/steps', 'sectionId' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default']) ?>
            <?php endif; ?>
        </div>
    <?= Html::endForm() ?>
</div>
