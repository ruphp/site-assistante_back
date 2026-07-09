<?php

use app\Modules\Support\Domain\SupportEntryPoint;
use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Support\Domain\SupportPlanLimit;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var SupportEntryPoint[] $entryPoints
 * @var SupportPlanLimit $limit
 * @var string $plan
 */

$this->title = 'Кнопки обращений';
$planLabel = SupportPlan::labels()[$plan] ?? $plan;
$currentRankLimit = min($limit->entryPointRankLimit(), max(1, count($entryPoints)));
$newRankLimit = min($limit->entryPointRankLimit(), max(1, count($entryPoints) + 1));
$currentRankOptions = [];
for ($rank = 1; $rank <= $currentRankLimit; $rank++) {
    $currentRankOptions[$rank] = (string)$rank;
}
$newRankOptions = [];
for ($rank = 1; $rank <= $newRankLimit; $rank++) {
    $newRankOptions[$rank] = (string)$rank;
}
?>

<div class="uk-container uk-position-relative">
    <h3>Кнопки обращений</h3>

    <div class="uk-alert-primary" uk-alert>
        <p><?= Html::encode($planLabel) ?>-тариф: <?= Html::encode((string)$limit->maxEntryPoints) ?> кнопка обращения. Обычный чат без кнопки остается доступен всегда с приоритетом 0.</p>
    </div>

    <?php if ($entryPoints !== []): ?>
        <table class="uk-table uk-table-divider uk-table-middle">
            <thead>
            <tr>
                <th>Название</th>
                <th>Ответ</th>
                <th>Приоритет</th>
                <th>Порядок</th>
                <th>Включена</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($entryPoints as $entryPoint): ?>
                <tr>
                    <?php $form = \ruwmapps\yii2_uikit3\ActiveForm::begin([
                        'action' => Url::to(['/manager/support/entry-points']),
                        'options' => ['class' => 'uk-form-stacked'],
                    ]); ?>
                    <?= Html::hiddenInput('SupportEntryPoint[id]', (string)$entryPoint->id) ?>
                    <td>
                        <?= Html::textarea('SupportEntryPoint[title]', $entryPoint->title, [
                            'class' => 'uk-textarea uk-form-width-medium',
                            'rows' => 2,
                            'maxlength' => 255,
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::textarea('SupportEntryPoint[description]', $entryPoint->description, [
                            'class' => 'uk-textarea uk-form-width-large',
                            'rows' => 2,
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::dropDownList('SupportEntryPoint[priority]', min($entryPoint->priority, $currentRankLimit), $currentRankOptions, [
                            'class' => 'uk-select uk-form-width-xsmall',
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::dropDownList('SupportEntryPoint[sortOrder]', min($entryPoint->sortOrder, $currentRankLimit), $currentRankOptions, [
                            'class' => 'uk-select uk-form-width-xsmall',
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::hiddenInput('SupportEntryPoint[enabled]', '0') ?>
                        <?= Html::checkbox('SupportEntryPoint[enabled]', $entryPoint->enabled, ['value' => '1']) ?>
                    </td>
                    <td class="uk-text-nowrap">
                        <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                        <?= Html::a('Удалить', ['/manager/support/entry-point/delete', 'id' => $entryPoint->id], [
                            'class' => 'uk-button uk-button-danger uk-button-small',
                            'data-method' => 'post',
                            'data-confirm' => 'Удалить кнопку обращения?',
                        ]) ?>
                    </td>
                    <?php \ruwmapps\yii2_uikit3\ActiveForm::end(); ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="uk-alert-primary" uk-alert>
            <p>Кнопок пока нет. Посетитель сможет начать обычный чат через поле сообщения.</p>
        </div>
    <?php endif; ?>

    <?php if (count($entryPoints) < $limit->maxEntryPoints): ?>
        <hr>
        <h4>Новая кнопка</h4>
        <?php $form = \ruwmapps\yii2_uikit3\ActiveForm::begin([
            'action' => Url::to(['/manager/support/entry-points']),
            'options' => ['class' => 'uk-form-stacked'],
        ]); ?>
        <?= Html::hiddenInput('SupportEntryPoint[id]', '') ?>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-3@s">
                <?= Html::label('Название', 'support-entry-title', ['class' => 'uk-form-label']) ?>
                <?= Html::textarea('SupportEntryPoint[title]', '', [
                    'id' => 'support-entry-title',
                    'class' => 'uk-textarea',
                    'rows' => 2,
                    'maxlength' => 255,
                    'placeholder' => 'Не работает сервис',
                ]) ?>
            </div>
            <div class="uk-width-1-3@s">
                <?= Html::label('Ответ', 'support-entry-description', ['class' => 'uk-form-label']) ?>
                <?= Html::textarea('SupportEntryPoint[description]', '', [
                    'id' => 'support-entry-description',
                    'class' => 'uk-textarea',
                    'rows' => 3,
                    'placeholder' => 'Что подставить в первое сообщение вместо названия проблемы',
                ]) ?>
            </div>
            <div class="uk-width-1-6@s">
                <?= Html::label('Приоритет', 'support-entry-priority', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('SupportEntryPoint[priority]', $newRankLimit, $newRankOptions, [
                    'id' => 'support-entry-priority',
                    'class' => 'uk-select',
                ]) ?>
            </div>
            <div class="uk-width-1-6@s">
                <?= Html::label('Порядок', 'support-entry-sort', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('SupportEntryPoint[sortOrder]', $newRankLimit, $newRankOptions, [
                    'id' => 'support-entry-sort',
                    'class' => 'uk-select',
                ]) ?>
            </div>
        </div>
        <div class="uk-margin">
            <?= Html::hiddenInput('SupportEntryPoint[enabled]', '0') ?>
            <label><?= Html::checkbox('SupportEntryPoint[enabled]', true, ['value' => '1']) ?> Показывать в виджете</label>
        </div>
        <?= Html::submitButton('Добавить кнопку', ['class' => 'uk-button uk-button-primary']) ?>
        <?php \ruwmapps\yii2_uikit3\ActiveForm::end(); ?>
    <?php endif; ?>
</div>
