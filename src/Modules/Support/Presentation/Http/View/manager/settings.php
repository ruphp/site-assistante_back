<?php

use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Domain\SupportSettings;
use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

/**
 * @var SupportSettings $settings
 * @var SupportPlanLimit $limit
 */

$this->title = 'Онлайн-поддержка';
$timezones = array_combine(DateTimeZone::listIdentifiers(), DateTimeZone::listIdentifiers());
if ($settings->timezone !== '' && !isset($timezones[$settings->timezone])) {
    $timezones = [$settings->timezone => $settings->timezone] + $timezones;
}
?>

<div class="uk-container uk-position-relative">
    <h3>Онлайн-поддержка</h3>

    <div class="uk-alert-primary" uk-alert>
        <p>Free-тариф: <?= Html::encode((string)$limit->maxOperators) ?> оператор, <?= Html::encode((string)$limit->maxConversationsPerMonth) ?> диалогов в месяц, <?= Html::encode((string)$limit->maxMessagesPerMonth) ?> сообщений в месяц, история <?= Html::encode((string)$limit->historyDays) ?> дней.</p>
    </div>

    <?php $form = ActiveForm::begin(['options' => ['class' => 'uk-form-stacked']]); ?>

    <div class="uk-margin">
        <?= Html::label('Название модуля', 'support-title', ['class' => 'uk-form-label']) ?>
        <?= Html::input('text', 'SupportSettings[title]', $settings->title, [
            'id' => 'support-title',
            'class' => 'uk-input uk-form-width-large',
            'maxlength' => 255,
        ]) ?>
    </div>

    <div class="uk-margin">
        <?= Html::label('Приветственное сообщение', 'support-welcome', ['class' => 'uk-form-label']) ?>
        <?= Html::textarea('SupportSettings[welcomeMessage]', $settings->welcomeMessage, [
            'id' => 'support-welcome',
            'class' => 'uk-textarea',
            'rows' => 4,
        ]) ?>
    </div>

    <div class="uk-margin">
        <?= Html::label('Сообщение, когда операторов нет онлайн', 'support-offline', ['class' => 'uk-form-label']) ?>
        <?= Html::textarea('SupportSettings[offlineMessage]', $settings->offlineMessage, [
            'id' => 'support-offline',
            'class' => 'uk-textarea',
            'rows' => 4,
        ]) ?>
    </div>

    <div class="uk-margin">
        <?= Html::label('Контакты или общая информация', 'support-contact-info', ['class' => 'uk-form-label']) ?>
        <?= Html::textarea('SupportSettings[contactInfo]', $settings->contactInfo, [
            'id' => 'support-contact-info',
            'class' => 'uk-textarea',
            'rows' => 5,
        ]) ?>
    </div>

    <div class="uk-grid-small" uk-grid>
        <div class="uk-width-1-3@s">
            <?= Html::label('Часовой пояс', 'support-timezone', ['class' => 'uk-form-label']) ?>
            <?= Html::dropDownList('SupportSettings[timezone]', $settings->timezone, $timezones, [
                'id' => 'support-timezone',
                'class' => 'uk-input',
            ]) ?>
        </div>
        <div class="uk-width-2-3@s">
            <?= Html::label('Рабочее время', 'support-working-hours', ['class' => 'uk-form-label']) ?>
            <?= Html::input('text', 'SupportSettings[workingHours]', $settings->workingHours, [
                'id' => 'support-working-hours',
                'class' => 'uk-input',
            ]) ?>
        </div>
    </div>

    <div class="uk-margin">
        <div class="uk-form-label">Форма посетителя</div>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[askName]', '0') ?>
            <?= Html::checkbox('SupportSettings[askName]', $settings->askName, ['value' => '1']) ?>
            Запрашивать имя
        </label>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[askEmail]', '0') ?>
            <?= Html::checkbox('SupportSettings[askEmail]', $settings->askEmail, ['value' => '1']) ?>
            Запрашивать email
        </label>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[askPhone]', '0') ?>
            <?= Html::checkbox('SupportSettings[askPhone]', $settings->askPhone, ['value' => '1']) ?>
            Запрашивать телефон
        </label>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[requireEmailOffline]', '0') ?>
            <?= Html::checkbox('SupportSettings[requireEmailOffline]', $settings->requireEmailOffline, ['value' => '1']) ?>
            Email обязателен, когда операторов нет онлайн
        </label>
    </div>

    <div class="uk-margin">
        <?= Html::label('Автоответ после первого сообщения', 'support-auto-reply', ['class' => 'uk-form-label']) ?>
        <?= Html::textarea('SupportSettings[autoReply]', $settings->autoReply, [
            'id' => 'support-auto-reply',
            'class' => 'uk-textarea',
            'rows' => 3,
        ]) ?>
    </div>

    <div class="uk-margin">
        <?= Html::label('Интервал опроса сообщений, секунд', 'support-polling', ['class' => 'uk-form-label']) ?>
        <?= Html::input('number', 'SupportSettings[pollingIntervalSeconds]', (string)$settings->pollingIntervalSeconds, [
            'id' => 'support-polling',
            'class' => 'uk-input uk-form-width-small',
            'min' => 3,
            'max' => 60,
        ]) ?>
    </div>

    <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>
