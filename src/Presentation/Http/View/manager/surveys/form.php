<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Infrastructure\YiiActiveRecord\Roles;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyFormRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyQuestionRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyUrlRecord;
use yii\helpers\Html;

/**
 * @var SurveyFormRecord $survey
 * @var SurveyQuestionRecord[] $questions
 * @var Roles[] $roles
 * @var int[] $selectedRoleIds
 * @var SurveyUrlRecord[] $surveyUrls
 * @var bool $urlBindingsEnabled
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = $survey->isNewRecord ? 'Создать анкету' : 'Редактировать анкету';
$typeOptions = [
    SurveyQuestionRecord::TYPE_TEXT => 'Текстовый ответ',
    SurveyQuestionRecord::TYPE_RADIO => 'Один вариант',
    SurveyQuestionRecord::TYPE_CHECKBOX => 'Несколько вариантов',
    SurveyQuestionRecord::TYPE_RATE => 'Оценка',
];
$urlText = implode("\n", array_map(static function (SurveyUrlRecord $url): string {
    return $url->url . '|' . ((bool)$url->include_children ? 'children' : '') . '|' . ((bool)$url->include_query ? 'query' : '');
}, $surveyUrls));
$renderQuestion = static function (?SurveyQuestionRecord $question, int $index) use ($typeOptions): string {
    $options = '';
    if ($question instanceof SurveyQuestionRecord) {
        $options = implode("\n", array_map(static fn($option): string => (string)$option->answer, $question->options));
    }

    ob_start();
    ?>
    <article class="sw-instruction-card sw-survey-question" data-survey-question>
        <div class="sw-instruction-card__top">
            <h4>Вопрос</h4>
            <button class="uk-button uk-button-default uk-button-small" type="button" data-remove-question>Удалить</button>
        </div>
        <div class="uk-margin-small">
            <label class="uk-form-label">Текст вопроса</label>
            <textarea class="uk-textarea" name="SurveyQuestion[title][]" rows="2"><?= Html::encode($question?->title ?? '') ?></textarea>
        </div>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-3@m">
                <label class="uk-form-label">Тип ответа</label>
                <?= Html::dropDownList('SurveyQuestion[type][]', $question?->type ?? SurveyQuestionRecord::TYPE_TEXT, $typeOptions, ['class' => 'uk-select']) ?>
            </div>
            <div class="uk-width-1-4@m">
                <label class="uk-form-label">Порядок</label>
                <input class="uk-input" type="number" name="SurveyQuestion[sort_order][]" value="<?= Html::encode((string)($question?->sort_order ?? (($index + 1) * 10))) ?>">
            </div>
            <div class="uk-width-1-4@m uk-flex uk-flex-bottom">
                <label><input class="uk-checkbox" type="checkbox" name="SurveyQuestion[is_required][<?= Html::encode((string)$index) ?>]" value="1" <?= $question?->is_required ? 'checked' : '' ?>> Обязательный</label>
            </div>
            <div class="uk-width-1-4@m uk-flex uk-flex-bottom">
                <label><input class="uk-checkbox" type="checkbox" name="SurveyQuestion[is_free_answer][<?= Html::encode((string)$index) ?>]" value="1" <?= $question?->is_free_answer ? 'checked' : '' ?>> Свой вариант</label>
            </div>
        </div>
        <div class="uk-margin-small">
            <label class="uk-form-label">Варианты ответа</label>
            <textarea class="uk-textarea" name="SurveyQuestion[options][]" rows="4" placeholder="Каждый вариант с новой строки"><?= Html::encode($options) ?></textarea>
            <div class="uk-text-meta">Для текстового ответа варианты можно оставить пустыми. Для оценки можно указать подписи шкалы.</div>
        </div>
    </article>
    <?php

    return (string)ob_get_clean();
};
$initialQuestions = $questions === [] ? [null] : $questions;
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3><?= Html::encode($this->title) ?></h3>
    <?= Html::beginForm('', 'post', ['class' => 'uk-form-stacked sw-survey-form']) ?>
        <div class="uk-margin">
            <label class="uk-form-label">Название анкеты</label>
            <input class="uk-input" name="Survey[title]" value="<?= Html::encode($survey->title) ?>" placeholder="Например: Оценка удобства страницы">
        </div>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-4@m">
                <label class="uk-form-label">Вопросов на экран</label>
                <input class="uk-input" type="number" min="1" name="Survey[question_count]" value="<?= Html::encode((string)($survey->question_count ?? '')) ?>" placeholder="Все сразу">
            </div>
            <div class="uk-width-1-4@m">
                <label class="uk-form-label">Порядок</label>
                <input class="uk-input" type="number" name="Survey[sort_order]" value="<?= Html::encode((string)$survey->sort_order) ?>">
            </div>
            <div class="uk-width-1-4@m">
                <label class="uk-form-label">Показывать с</label>
                <input class="uk-input" type="date" name="Survey[date_start]" value="<?= Html::encode((string)$survey->date_start) ?>">
            </div>
            <div class="uk-width-1-4@m">
                <label class="uk-form-label">Показывать до</label>
                <input class="uk-input" type="date" name="Survey[date_finish]" value="<?= Html::encode((string)$survey->date_finish) ?>">
            </div>
        </div>
        <div class="uk-margin uk-flex uk-flex-wrap uk-grid-small" uk-grid>
            <label><input class="uk-checkbox" type="checkbox" name="Survey[is_active]" value="1" <?= $survey->is_active ? 'checked' : '' ?>> Включена</label>
            <label><input class="uk-checkbox" type="checkbox" name="Survey[is_important]" value="1" <?= $survey->is_important ? 'checked' : '' ?>> Важная анкета</label>
        </div>

        <div class="uk-margin">
            <div class="uk-flex uk-flex-between uk-flex-middle">
                <h4 class="uk-margin-remove">Вопросы</h4>
                <button class="uk-button uk-button-default uk-button-small" type="button" data-add-question>Добавить вопрос</button>
            </div>
            <div class="sw-instruction-grid uk-margin-small-top" data-questions>
                <?php foreach ($initialQuestions as $index => $question): ?>
                    <?= $renderQuestion($question, $index) ?>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($roles !== []): ?>
            <div class="uk-margin">
                <label class="uk-form-label">Роли пользователя сайта</label>
                <div class="sw-instruction-role-list">
                    <?php foreach ($roles as $role): ?>
                        <label>
                            <input class="uk-checkbox" type="checkbox" name="Survey[role_ids][]" value="<?= Html::encode((string)$role->id) ?>" <?= in_array((int)$role->id, array_map('intval', $selectedRoleIds), true) ? 'checked' : '' ?>>
                            <?= Html::encode($role->name) ?> <span class="uk-text-meta">ID <?= Html::encode((string)$role->id_role_in_system) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="uk-text-meta">Если роли не выбраны, анкета доступна всем посетителям.</div>
            </div>
        <?php endif; ?>

        <div class="uk-margin">
            <label class="uk-form-label">URL-привязки</label>
            <?php if ($urlBindingsEnabled): ?>
                <textarea class="uk-textarea" name="Survey[urls]" rows="5" placeholder="/catalog|children&#10;/checkout||query"><?= Html::encode($urlText) ?></textarea>
                <div class="uk-text-meta">Формат строки: URL|children|query. Если список пустой, анкета видна на всех страницах.</div>
            <?php else: ?>
                <div class="uk-alert-primary" uk-alert>URL-привязки доступны в Pro-тарифе. Сейчас анкета будет общей или ограниченной только ролями.</div>
            <?php endif; ?>
        </div>

        <button class="uk-button uk-button-primary" type="submit">Сохранить</button>
        <?= Html::a('Отмена', ['/manager/surveys', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default']) ?>
    <?= Html::endForm() ?>
</div>

<template data-question-template>
    <?= $renderQuestion(null, 9999) ?>
</template>

<?php
$this->registerJs(<<<'JS'
(function () {
    const list = document.querySelector('[data-questions]');
    const template = document.querySelector('[data-question-template]');
    const addButton = document.querySelector('[data-add-question]');
    if (!list || !template || !addButton) {
        return;
    }

    const renumberCheckboxNames = () => {
        list.querySelectorAll('[data-survey-question]').forEach((card, index) => {
            card.querySelectorAll('input[type="checkbox"][name^="SurveyQuestion"]').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
            });
        });
    };

    addButton.addEventListener('click', () => {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.trim();
        list.appendChild(wrapper.firstElementChild);
        renumberCheckboxNames();
    });

    list.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-question]');
        if (!button) {
            return;
        }
        const cards = list.querySelectorAll('[data-survey-question]');
        if (cards.length <= 1) {
            return;
        }
        button.closest('[data-survey-question]')?.remove();
        renumberCheckboxNames();
    });

    renumberCheckboxNames();
})();
JS);
?>
