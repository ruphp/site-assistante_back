<?php

use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Domain\SupportSettings;
use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

/**
 * @var SupportSettings $settings
 * @var SupportPlanLimit $limit
 * @var array<int, \app\Modules\Support\Application\Dto\SupportManagerRecipient> $managerRecipients
 * @var string $defaultNotificationEmail
 */

$this->title = 'Онлайн-поддержка';
$notificationEmails = $settings->notificationEmails !== '' ? $settings->notificationEmails : $defaultNotificationEmail;
$schedule = $settings->normalizedWorkSchedule();
$scheduleDays = $schedule['days'] ?? [];
$holidaySchedule = $settings->holidaySchedule;
$weekDays = [
    'mon' => 'Понедельник',
    'tue' => 'Вторник',
    'wed' => 'Среда',
    'thu' => 'Четверг',
    'fri' => 'Пятница',
    'sat' => 'Суббота',
    'sun' => 'Воскресенье',
];
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
    </div>

    <div class="uk-margin">
        <div class="uk-form-label">Рабочее время операторов</div>
        <div class="uk-grid-small uk-child-width-auto@s" uk-grid>
            <label>
                <?= Html::radio('SupportSchedule[mode]', ($schedule['mode'] ?? 'weekdays') === 'weekdays', ['value' => 'weekdays', 'class' => 'js-support-schedule-mode']) ?>
                Пн-Пт
            </label>
            <label>
                <?= Html::radio('SupportSchedule[mode]', ($schedule['mode'] ?? 'weekdays') === 'everyday', ['value' => 'everyday', 'class' => 'js-support-schedule-mode']) ?>
                Без выходных
            </label>
            <label>
                <?= Html::radio('SupportSchedule[mode]', ($schedule['mode'] ?? 'weekdays') === 'custom', ['value' => 'custom', 'class' => 'js-support-schedule-mode']) ?>
                По дням
            </label>
            <label>
                <?= Html::hiddenInput('SupportSchedule[roundTheClock]', '0') ?>
                <?= Html::checkbox('SupportSchedule[roundTheClock]', (bool)($schedule['round_the_clock'] ?? false), ['value' => '1', 'id' => 'support-round-the-clock']) ?>
                Круглосуточно
            </label>
        </div>

        <table class="uk-table uk-table-small uk-table-divider uk-margin-small-top">
            <thead>
            <tr>
                <th>День</th>
                <th>Работает</th>
                <th>С</th>
                <th>До</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($weekDays as $dayKey => $dayLabel): ?>
                <?php $day = $scheduleDays[$dayKey] ?? ['enabled' => false, 'from' => '09:00', 'to' => '18:00']; ?>
                <tr data-support-week-day="<?= Html::encode($dayKey) ?>">
                    <td><?= Html::encode($dayLabel) ?></td>
                    <td>
                        <?= Html::hiddenInput("SupportSchedule[days][$dayKey][enabled]", '0') ?>
                        <?= Html::checkbox("SupportSchedule[days][$dayKey][enabled]", (bool)($day['enabled'] ?? false), [
                            'value' => '1',
                            'class' => 'js-support-day-enabled',
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::input('time', "SupportSchedule[days][$dayKey][from]", (string)($day['from'] ?? '09:00'), [
                            'class' => 'uk-input uk-form-width-small js-support-day-time',
                            'step' => 600,
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::input('time', "SupportSchedule[days][$dayKey][to]", (string)($day['to'] ?? '18:00'), [
                            'class' => 'uk-input uk-form-width-small js-support-day-time',
                            'step' => 600,
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="uk-margin">
        <div class="uk-form-label">Праздничные дни и исключения</div>
        <div class="uk-text-meta">Можно указать до 10 дней: праздничный день, внеплановый выходной или отдельные часы работы.</div>
        <?php
        $holidayRows = array_values($holidaySchedule);
        while (count($holidayRows) < 10) {
            $holidayRows[] = ['date' => '', 'closed' => true, 'from' => '09:00', 'to' => '18:00'];
        }
        $holidayRows = array_slice($holidayRows, 0, 10);
        ?>
        <table class="uk-table uk-table-small uk-table-divider uk-margin-small-top">
            <thead>
            <tr>
                <th>Дата</th>
                <th>Выходной</th>
                <th>С</th>
                <th>До</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($holidayRows as $index => $holiday): ?>
                <tr data-support-holiday-row>
                    <td>
                        <?= Html::input('date', "SupportSchedule[holidays][$index][date]", (string)($holiday['date'] ?? ''), [
                            'class' => 'uk-input uk-form-width-medium js-support-holiday-date',
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::hiddenInput("SupportSchedule[holidays][$index][closed]", '0') ?>
                        <?= Html::checkbox("SupportSchedule[holidays][$index][closed]", (bool)($holiday['closed'] ?? true), [
                            'value' => '1',
                            'class' => 'js-support-holiday-closed',
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::input('time', "SupportSchedule[holidays][$index][from]", (string)($holiday['from'] ?? '09:00'), [
                            'class' => 'uk-input uk-form-width-small js-support-holiday-time',
                            'step' => 600,
                        ]) ?>
                    </td>
                    <td>
                        <?= Html::input('time', "SupportSchedule[holidays][$index][to]", (string)($holiday['to'] ?? '18:00'), [
                            'class' => 'uk-input uk-form-width-small js-support-holiday-time',
                            'step' => 600,
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="uk-margin">
        <div class="uk-form-label">Данные посетителя</div>
        <div class="uk-text-meta uk-margin-small-bottom">
            Эти поля спрашиваются всплывашкой при старте диалога только если сайт не передал их в коде подключения.
        </div>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[askName]', '0') ?>
            <?= Html::checkbox('SupportSettings[askName]', $settings->askName, ['value' => '1']) ?>
            Запрашивать имя, если не передано сайтом
        </label>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[askEmail]', '0') ?>
            <?= Html::checkbox('SupportSettings[askEmail]', $settings->askEmail, ['value' => '1']) ?>
            Запрашивать email, если не передан сайтом
        </label>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[askPhone]', '0') ?>
            <?= Html::checkbox('SupportSettings[askPhone]', $settings->askPhone, ['value' => '1']) ?>
            Запрашивать телефон, если не передан сайтом
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

    <hr>

    <div class="uk-margin">
        <div class="uk-form-label">Уведомления менеджеров</div>
        <div class="uk-alert-primary" uk-alert>
            <p>Админка включена всегда: новые обращения будут видны в разделе онлайн-поддержки.</p>
        </div>
        <?php if ($managerRecipients === []): ?>
            <div class="uk-alert-warning" uk-alert>
                <p>Активных менеджеров у клиента пока не найдено. Добавьте email ниже вручную.</p>
            </div>
        <?php else: ?>
            <div class="uk-margin-small">
                <div class="uk-text-meta">Менеджеры клиента из аккаунтов</div>
                <ul class="uk-list uk-list-small uk-margin-small-top">
                    <?php foreach ($managerRecipients as $recipient): ?>
                        <li>
                            <?= Html::encode($recipient->name !== '' ? $recipient->name : ('#' . $recipient->id)) ?>
                            <span class="uk-text-muted"><?= Html::encode($recipient->email) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[notifyEmail]', '0') ?>
            <?= Html::checkbox('SupportSettings[notifyEmail]', $settings->notifyEmail, ['value' => '1']) ?>
            Email менеджерам
        </label>
        <div class="uk-margin-small-top">
            <?= Html::label('Email для уведомлений через запятую', 'support-notification-emails', ['class' => 'uk-form-label']) ?>
            <?= Html::textarea('SupportSettings[notificationEmails]', $notificationEmails, [
                'id' => 'support-notification-emails',
                'class' => 'uk-textarea',
                'rows' => 2,
                'placeholder' => 'manager@example.ru, support@example.ru',
            ]) ?>
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[notifyTelegram]', '0') ?>
            <?= Html::checkbox('SupportSettings[notifyTelegram]', $settings->notifyTelegram, ['value' => '1']) ?>
            Telegram
        </label>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-2@s">
                <?= Html::label('Telegram bot token', 'support-telegram-token', ['class' => 'uk-form-label']) ?>
                <?= Html::input('text', 'SupportSettings[telegramBotToken]', $settings->telegramBotToken, [
                    'id' => 'support-telegram-token',
                    'class' => 'uk-input',
                    'autocomplete' => 'off',
                ]) ?>
            </div>
            <div class="uk-width-1-2@s">
                <?= Html::label('Telegram chat id', 'support-telegram-chat', ['class' => 'uk-form-label']) ?>
                <?= Html::input('text', 'SupportSettings[telegramChatId]', $settings->telegramChatId, [
                    'id' => 'support-telegram-chat',
                    'class' => 'uk-input',
                    'autocomplete' => 'off',
                ]) ?>
            </div>
        </div>
    </div>

    <div class="uk-margin">
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[notifyMax]', '0') ?>
            <?= Html::checkbox('SupportSettings[notifyMax]', $settings->notifyMax, ['value' => '1']) ?>
            MAX
        </label>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-3@s">
                <?= Html::label('MAX API URL', 'support-max-api-url', ['class' => 'uk-form-label']) ?>
                <?= Html::input('text', 'SupportSettings[maxApiUrl]', $settings->maxApiUrl, [
                    'id' => 'support-max-api-url',
                    'class' => 'uk-input',
                    'autocomplete' => 'off',
                ]) ?>
            </div>
            <div class="uk-width-1-3@s">
                <?= Html::label('MAX bot token', 'support-max-token', ['class' => 'uk-form-label']) ?>
                <?= Html::input('text', 'SupportSettings[maxBotToken]', $settings->maxBotToken, [
                    'id' => 'support-max-token',
                    'class' => 'uk-input',
                    'autocomplete' => 'off',
                ]) ?>
            </div>
            <div class="uk-width-1-3@s">
                <?= Html::label('MAX chat id', 'support-max-chat', ['class' => 'uk-form-label']) ?>
                <?= Html::input('text', 'SupportSettings[maxChatId]', $settings->maxChatId, [
                    'id' => 'support-max-chat',
                    'class' => 'uk-input',
                    'autocomplete' => 'off',
                ]) ?>
            </div>
        </div>
    </div>

    <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<'JS'
(function () {
    var weekdayKeys = ['mon', 'tue', 'wed', 'thu', 'fri'];
    var weekendKeys = ['sat', 'sun'];
    var modeInputs = document.querySelectorAll('.js-support-schedule-mode');
    var roundTheClock = document.getElementById('support-round-the-clock');

    function selectedMode() {
        var checked = document.querySelector('.js-support-schedule-mode:checked');
        return checked ? checked.value : 'weekdays';
    }

    function setMuted(element, muted) {
        if (!element) {
            return;
        }

        element.classList.toggle('uk-text-muted', muted);
        element.style.opacity = muted ? '0.55' : '';
    }

    function setCheckboxState(checkbox, checked, disabled) {
        if (!checkbox) {
            return;
        }

        if (checked !== null) {
            checkbox.checked = checked;
        }

        checkbox.disabled = disabled;
    }

    function updateWeekRows() {
        var mode = selectedMode();
        var isRoundTheClock = roundTheClock && roundTheClock.checked;

        document.querySelectorAll('[data-support-week-day]').forEach(function (row) {
            var dayKey = row.getAttribute('data-support-week-day');
            var enabled = row.querySelector('.js-support-day-enabled');
            var timeInputs = row.querySelectorAll('.js-support-day-time');
            var isWeekend = weekendKeys.indexOf(dayKey) !== -1;
            var forcedEnabled = null;
            var checkboxLocked = false;

            if (mode === 'everyday') {
                forcedEnabled = true;
                checkboxLocked = true;
            }

            if (mode === 'weekdays') {
                forcedEnabled = !isWeekend;
                checkboxLocked = true;
            }

            setCheckboxState(enabled, forcedEnabled, checkboxLocked);

            var dayWorks = enabled && enabled.checked;
            var timeDisabled = isRoundTheClock || !dayWorks;
            timeInputs.forEach(function (input) {
                input.readOnly = timeDisabled;
                input.setAttribute('aria-disabled', timeDisabled ? 'true' : 'false');
                setMuted(input, timeDisabled);
            });
        });
    }

    function updateHolidayRows() {
        var rows = Array.prototype.slice.call(document.querySelectorAll('[data-support-holiday-row]'));
        var lastFilledIndex = -1;

        rows.forEach(function (row, index) {
            var date = row.querySelector('.js-support-holiday-date');
            if (date && date.value) {
                lastFilledIndex = index;
            }
        });

        rows.forEach(function (row, index) {
            row.hidden = index > Math.min(lastFilledIndex + 1, rows.length - 1);

            var closed = row.querySelector('.js-support-holiday-closed');
            var timeInputs = row.querySelectorAll('.js-support-holiday-time');
            var timeDisabled = closed && closed.checked;

            timeInputs.forEach(function (input) {
                input.readOnly = timeDisabled;
                input.setAttribute('aria-disabled', timeDisabled ? 'true' : 'false');
                setMuted(input, timeDisabled);
            });
        });
    }

    function updateAll() {
        updateWeekRows();
        updateHolidayRows();
    }

    modeInputs.forEach(function (input) {
        input.addEventListener('change', updateAll);
    });

    if (roundTheClock) {
        roundTheClock.addEventListener('change', updateAll);
    }

    document.querySelectorAll('.js-support-day-enabled, .js-support-holiday-closed').forEach(function (input) {
        input.addEventListener('change', updateAll);
    });

    document.querySelectorAll('.js-support-holiday-date').forEach(function (input) {
        input.addEventListener('change', updateAll);
        input.addEventListener('input', updateAll);
    });

    updateAll();
})();
JS, \yii\web\View::POS_READY);
?>
