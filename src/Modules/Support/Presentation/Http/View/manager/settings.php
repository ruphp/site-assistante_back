<?php

use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Domain\SupportSettings;
use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

/**
 * @var SupportSettings $settings
 * @var SupportPlanLimit $limit
 * @var array<int, \app\Modules\Support\Application\Dto\SupportManagerRecipient> $managerRecipients
 * @var string $defaultNotificationEmail
 * @var \app\Application\Panel\Dto\ClientProjectView[] $projects
 * @var \app\Application\Panel\Dto\ClientProjectView $activeProject
 */

$this->title = 'Онлайн-поддержка';
$planLabel = SupportPlan::labels()[$settings->plan] ?? $settings->plan;
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
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>

    <h3>Онлайн-поддержка</h3>

    <div class="uk-alert-primary" uk-alert>
        <p><?= Html::encode($planLabel) ?>-тариф: <?= Html::encode((string)$limit->maxOperators) ?> оператор, <?= Html::encode((string)$limit->maxConversationsPerMonth) ?> диалогов в месяц, <?= Html::encode((string)$limit->maxMessagesPerMonth) ?> сообщений в месяц, история <?= Html::encode((string)$limit->historyDays) ?> дней.</p>
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
        <?= Html::label('Приветственное сообщение при онлайне', 'support-welcome', ['class' => 'uk-form-label']) ?>
        <?= Html::textarea('SupportSettings[welcomeMessage]', $settings->welcomeMessage, [
            'id' => 'support-welcome',
            'class' => 'uk-textarea',
            'rows' => 4,
        ]) ?>
    </div>

    <div class="uk-margin">
        <?= Html::label('Приветственное сообщение при офлайне', 'support-offline', ['class' => 'uk-form-label']) ?>
        <?= Html::textarea('SupportSettings[offlineMessage]', $settings->offlineMessage, [
            'id' => 'support-offline',
            'class' => 'uk-textarea',
            'rows' => 4,
        ]) ?>
    </div>

    <div class="uk-margin">
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[keepWidgetOpenWhenOnline]', '0') ?>
            <?= Html::checkbox('SupportSettings[keepWidgetOpenWhenOnline]', $settings->keepWidgetOpenWhenOnline, [
                'value' => '1',
                'id' => 'support-keep-widget-open',
            ]) ?>
            Когда операторы онлайн, держать виджет открытым
        </label>
    </div>

    <div class="uk-margin" id="support-auto-open-snooze-field">
        <?= Html::label('Не открывать повторно после закрытия, минут', 'support-auto-open-snooze', ['class' => 'uk-form-label']) ?>
        <?= Html::input('number', 'SupportSettings[autoOpenSnoozeMinutes]', (string)$settings->autoOpenSnoozeMinutes, [
            'id' => 'support-auto-open-snooze',
            'class' => 'uk-input uk-form-width-small',
            'min' => 0,
            'max' => 1440,
            'step' => 1,
        ]) ?>
        <div class="uk-text-meta">0 - автооткрытие будет срабатывать каждый раз.</div>
    </div>

    <div class="uk-margin">
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[showBranding]', '0') ?>
            <?= Html::checkbox('SupportSettings[showBranding]', $settings->showBranding || $settings->plan === SupportPlan::FREE, [
                'value' => '1',
                'disabled' => $settings->plan === SupportPlan::FREE,
            ]) ?>
            Показывать ссылку SiteWidget.ru в виджете
        </label>
        <div class="uk-text-meta">
            На Free-тарифе ссылка обязательна. В платных тарифах ее можно скрыть.
        </div>
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
            Имя, email и телефон запрашиваются всегда. Email обязателен, обязательность имени и телефона можно настроить.
        </div>
        <label class="uk-display-block">
            <?= Html::hiddenInput('SupportSettings[askName]', '0') ?>
            <?= Html::checkbox('SupportSettings[askName]', $settings->askName, ['value' => '1']) ?>
            Имя обязательно
        </label>
        <label class="uk-display-block uk-margin-small-top">
            <?= Html::hiddenInput('SupportSettings[askPhone]', '0') ?>
            <?= Html::checkbox('SupportSettings[askPhone]', $settings->askPhone, ['value' => '1']) ?>
            Телефон обязательно
        </label>
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
            Email
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
    var keepWidgetOpen = document.getElementById('support-keep-widget-open');
    var autoOpenSnoozeField = document.getElementById('support-auto-open-snooze-field');

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
        if (autoOpenSnoozeField && keepWidgetOpen) {
            autoOpenSnoozeField.hidden = !keepWidgetOpen.checked;
        }
    }

    modeInputs.forEach(function (input) {
        input.addEventListener('change', updateAll);
    });

    if (roundTheClock) {
        roundTheClock.addEventListener('change', updateAll);
    }

    if (keepWidgetOpen) {
        keepWidgetOpen.addEventListener('change', updateAll);
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
