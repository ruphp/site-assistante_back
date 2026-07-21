<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Infrastructure\YiiActiveRecord\Roles;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
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
 * @var InstructionArticleRecord[] $instructions
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = $hint->isNewRecord ? 'Создать подсказку' : 'Редактировать подсказку';
$positions = [0 => 'Сверху', 1 => 'Справа', 2 => 'Снизу', 3 => 'Слева'];
$bindModes = [0 => 'К элементу', 1 => 'Поверх страницы'];
$themes = ['light' => 'Светлая', 'dark' => 'Темная'];
$triggers = ['hover' => 'При наведении', 'click' => 'По клику'];
$icons = ['question' => '?', 'exclamation' => '!', 'none' => 'Без символа'];
$contentMaxLength = 300;
$instructionOptions = ['' => 'Не открывать инструкцию'] + ArrayHelper::map($instructions, 'id', 'title');
$urlsText = implode("\n", array_map(static fn(OnboardingHintUrlRecord $url): string => $url->url . ($url->include_children ? '|children' : '') . ($url->include_query ? '|query' : ''), $hintUrls));
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', [
        'projects' => $projects,
        'activeProject' => $activeProject,
        'projectTabsPath' => '/manager/onboarding/hints',
    ]) ?>
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
            <?= Html::textarea('OnboardingHint[content]', $hint->content, [
                'id' => 'hint-content',
                'class' => 'uk-textarea',
                'rows' => 5,
                'data-max-weight' => $contentMaxLength,
                'data-line-weight' => 60,
            ]) ?>
            <div class="uk-text-meta">
                До <?= $contentMaxLength ?> условных символов. Пробел считается символом, перенос строки считается как 60 символов.
                <span id="hint-content-counter"></span>
            </div>
        </div>
        <div class="uk-grid-small uk-child-width-1-3@m" uk-grid>
            <div>
                <?= Html::label('Стиль подсказки', 'hint-theme', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('OnboardingHint[theme]', $hint->theme ?: 'light', $themes, ['id' => 'hint-theme', 'class' => 'uk-select']) ?>
            </div>
            <div>
                <?= Html::label('Как открывать', 'hint-trigger', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('OnboardingHint[trigger_type]', $hint->trigger_type ?: 'hover', $triggers, ['id' => 'hint-trigger', 'class' => 'uk-select']) ?>
            </div>
            <div>
                <?= Html::label('Символ на точке', 'hint-icon', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('OnboardingHint[icon_type]', $hint->icon_type ?: 'question', $icons, ['id' => 'hint-icon', 'class' => 'uk-select']) ?>
            </div>
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
            <label><?= Html::checkbox('OnboardingHint[hide_after_view]', (bool)$hint->hide_after_view) ?> Прятать после просмотра</label>
        </div>
        <div class="uk-margin">
            <?= Html::label('Способ привязки', 'hint-bind-mode', ['class' => 'uk-form-label']) ?>
            <?= Html::dropDownList('OnboardingHint[type_bind]', (int)(bool)$hint->type_bind, $bindModes, ['id' => 'hint-bind-mode', 'class' => 'uk-select uk-form-width-medium']) ?>
        </div>
        <div class="uk-card uk-card-default uk-card-small uk-card-body uk-margin">
            <h4 class="uk-margin-small-bottom">Кнопка в подсказке</h4>
            <div class="uk-grid-small uk-child-width-1-3@m" uk-grid>
                <div>
                    <?= Html::label('Текст кнопки', 'hint-button-label', ['class' => 'uk-form-label']) ?>
                    <?= Html::input('text', 'OnboardingHint[button_label]', $hint->button_label, ['id' => 'hint-button-label', 'class' => 'uk-input', 'placeholder' => 'Например: Подробнее']) ?>
                </div>
                <div>
                    <?= Html::label('Ссылка', 'hint-button-url', ['class' => 'uk-form-label']) ?>
                    <?= Html::input('text', 'OnboardingHint[button_url]', $hint->button_url, ['id' => 'hint-button-url', 'class' => 'uk-input', 'placeholder' => 'https://...']) ?>
                </div>
                <div>
                    <?= Html::label('Или инструкция', 'hint-button-instruction', ['class' => 'uk-form-label']) ?>
                    <?= Html::dropDownList('OnboardingHint[button_instruction_id]', $hint->button_instruction_id, $instructionOptions, ['id' => 'hint-button-instruction', 'class' => 'uk-select']) ?>
                </div>
            </div>
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

<?php
$this->registerJs(<<<JS
(function () {
    var input = document.getElementById('hint-content');
    var counter = document.getElementById('hint-content-counter');
    if (!input || !counter) return;
    var max = Number(input.dataset.maxWeight || 500);
    var lineWeight = Number(input.dataset.lineWeight || 60);

    function weight(value) {
        var total = 0;
        Array.from(value.replace(/\\r\\n|\\r/g, '\\n')).forEach(function (char) {
            total += char === '\\n' ? lineWeight : 1;
        });
        return total;
    }

    function sync() {
        var current = weight(input.value);
        var isOverLimit = current > max;
        input.classList.toggle('sw-field-over-limit', isOverLimit);
        counter.classList.toggle('sw-text-over-limit', isOverLimit);
        counter.textContent = isOverLimit
            ? ' Превышение: ' + (current - max) + '.'
            : ' Осталось: ' + (max - current) + '.';
    }

    input.addEventListener('input', sync);
    sync();
})();
JS);

$this->registerCss(<<<CSS
.sw-field-over-limit {
    border-color: #e3342f !important;
    box-shadow: 0 0 0 1px #e3342f inset;
}

.sw-text-over-limit {
    color: #e3342f;
    font-weight: 700;
}
CSS);
?>
