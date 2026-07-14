<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Infrastructure\YiiActiveRecord\Roles;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintUrlRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var OnboardingHintRecord $hint
 * @var Roles[] $roles
 * @var int[] $selectedRoleIds
 * @var OnboardingHintUrlRecord[] $hintUrls
 * @var bool $urlBindingsEnabled
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = $hint->isNewRecord ? 'Создать подсказку' : 'Редактировать подсказку';
$positions = [0 => 'Сверху', 1 => 'Справа', 2 => 'Снизу', 3 => 'Слева'];
$bindModes = [0 => 'К элементу', 1 => 'Поверх страницы'];
$urlsText = implode("\n", array_map(static fn(OnboardingHintUrlRecord $url): string => $url->url . ($url->include_children ? '|children' : '') . ($url->include_query ? '|query' : ''), $hintUrls));
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3><?= Html::encode($this->title) ?></h3>
    <?= Html::beginForm('', 'post', ['class' => 'uk-form-stacked sw-form-panel']) ?>
        <div class="uk-margin">
            <?= Html::label('Название', 'hint-title', ['class' => 'uk-form-label']) ?>
            <?= Html::input('text', 'OnboardingHint[title]', $hint->title, ['id' => 'hint-title', 'class' => 'uk-input', 'required' => true]) ?>
        </div>
        <div class="uk-margin">
            <?= Html::label('CSS-селектор элемента', 'hint-selector', ['class' => 'uk-form-label']) ?>
            <?= Html::input('text', 'OnboardingHint[selector]', $hint->selector, ['id' => 'hint-selector', 'class' => 'uk-input']) ?>
            <?= $this->render('_selectorPicker', [
                'inputId' => 'hint-selector',
                'defaultUrl' => $activeProject->domain,
                'target' => 'hint',
            ]) ?>
        </div>
        <div class="uk-margin">
            <?= Html::label('Текст подсказки', 'hint-content', ['class' => 'uk-form-label']) ?>
            <?= Html::textarea('OnboardingHint[content]', $hint->content, ['id' => 'hint-content', 'class' => 'uk-textarea', 'rows' => 5]) ?>
        </div>
        <div class="uk-grid-small uk-child-width-1-3@m" uk-grid>
            <div>
                <?= Html::label('Позиция', 'hint-position', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('OnboardingHint[position]', $hint->position, $positions, ['id' => 'hint-position', 'class' => 'uk-select']) ?>
            </div>
            <div>
                <?= Html::label('Сдвиг X', 'hint-left', ['class' => 'uk-form-label']) ?>
                <?= Html::input('number', 'OnboardingHint[left_offset]', (string)$hint->left_offset, ['id' => 'hint-left', 'class' => 'uk-input']) ?>
            </div>
            <div>
                <?= Html::label('Сдвиг Y', 'hint-top', ['class' => 'uk-form-label']) ?>
                <?= Html::input('number', 'OnboardingHint[top_offset]', (string)$hint->top_offset, ['id' => 'hint-top', 'class' => 'uk-input']) ?>
            </div>
        </div>
        <div class="uk-margin">
            <label><?= Html::checkbox('OnboardingHint[is_active]', (bool)$hint->is_active) ?> Включена</label>
        </div>
        <div class="uk-margin">
            <label><?= Html::checkbox('OnboardingHint[standalone_enabled]', (bool)$hint->standalone_enabled) ?> Показывать отдельно от онбординга</label>
        </div>
        <div class="uk-margin">
            <?= Html::label('Способ привязки', 'hint-bind-mode', ['class' => 'uk-form-label']) ?>
            <?= Html::dropDownList('OnboardingHint[type_bind]', (int)(bool)$hint->type_bind, $bindModes, ['id' => 'hint-bind-mode', 'class' => 'uk-select uk-form-width-medium']) ?>
        </div>
        <?php if ($roles !== []): ?>
            <div class="uk-margin">
                <div class="uk-form-label">Роли</div>
                <?= Html::checkboxList('role_ids', $selectedRoleIds, ArrayHelper::map($roles, 'id', 'name'), ['separator' => '<br>']) ?>
            </div>
        <?php endif; ?>
        <?php if ($urlBindingsEnabled): ?>
            <div class="uk-margin">
                <?= Html::label('URL-привязки', 'hint-urls', ['class' => 'uk-form-label']) ?>
                <?= Html::textarea('OnboardingHint[urls]', $urlsText, ['id' => 'hint-urls', 'class' => 'uk-textarea', 'rows' => 4, 'placeholder' => "/catalog|children\n/checkout|query"]) ?>
            </div>
        <?php endif; ?>
        <div class="uk-margin">
            <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>
            <?= Html::a('Отмена', ['/manager/onboarding/hints', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default']) ?>
        </div>
    <?= Html::endForm() ?>
</div>
