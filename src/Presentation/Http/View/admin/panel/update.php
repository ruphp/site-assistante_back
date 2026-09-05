<?php

use ruwmapps\yii2_uikit3\ActiveForm;
use app\Infrastructure\YiiActiveRecord\Users;
use yii\helpers\Html;

/**
 * @var Users $user
 * @var \app\Application\Admin\Dto\ClientModuleAccessView $moduleAccessView
 * @var \app\Modules\Support\Domain\SupportSettings $supportSettings
 * @var array<string, string> $supportPlanLabels
 */

$this->title = 'Изменение данных клиента';
?>
<div class="uk-container uk-container-xsmall">
    <div>
        <div class="uk-card uk-card-large uk-card-default uk-card-body">
            <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
                <h2 class="bd-title uk-margin-remove">Изменение данных клиента</h2>
                <?= Html::a('<span uk-icon="eye"></span> Просмотр', '/admin/clients/view?id=' . (int)$user->id, [
                    'class' => 'uk-button uk-button-default uk-button-small',
                    'encode' => false,
                ]) ?>
            </div>
            <?php
            app\Presentation\Yii\Asset\AppAsset::register($this);
            $form = ActiveForm::begin(['id' => 'user-join-form', 'classForm' => 'uk-form-stacked']);
            $user->change_password = 0;
            $user->modules = $moduleAccessView->selectedModules();
            $supportPlan = $supportSettings->plan;
            ?>
            <?= $form->field($user, 'firm') ?>
            <?= $form->field($user, 'name') ?>
            <?= $form->field($user, 'email')->label('Адрес электронной почты') ?>

            <?php
            if (($_ENV['TYPE_DEPLOYED'] ?? '') === 'MIRS') {
                echo $form->field($user, 'gmt')->hiddenInput()->label('');
            } else {
                echo $form->field($user, 'gmt')->label('Сдвиг времени сервиса GMT');
            }
            ?>

            <?= $form->field($user, 'status')->checkbox(['label' => 'Доступность контента']); ?>
            <?= $form->field($user, 'change_password')->checkbox(['label' => 'Cменить пароль']); ?>

            <h4>Разрешить использование модулей</h4>
            <?php
            foreach ($moduleAccessView->items() as $item) {
                if ($item->key === 'support') {
                    echo Html::hiddenInput("Users[modules][$item->key]", '1');
                    echo '<div class="uk-margin-small uk-text-muted">' . Html::encode($item->label) . ' включена всегда</div>';
                    continue;
                }

                echo $form->field($user, "modules[$item->key]")->checkbox(['label' => $item->label]);
            }
            ?>

            <h4>Тарифы</h4>
            <div class="uk-margin">
                <?= Html::label('Тариф', 'support-plan', ['class' => 'uk-form-label']) ?>
                <?= Html::dropDownList('Users[support_plan]', $supportPlan, $supportPlanLabels, [
                    'id' => 'support-plan',
                    'class' => 'uk-select',
                ]) ?>
            </div>
            <div class="uk-margin">
                <?= Html::label('Тариф действует до', 'support-plan-expires-at', ['class' => 'uk-form-label']) ?>
                <?= Html::input(
                    'datetime-local',
                    'Users[support_plan_expires_at]',
                    $supportSettings->planExpiresAt === null
                        ? ''
                        : date('Y-m-d\\TH:i', strtotime($supportSettings->planExpiresAt)),
                    ['id' => 'support-plan-expires-at', 'class' => 'uk-input']
                ) ?>
                <div class="uk-text-meta">Пустое значение означает тариф без ограничения по сроку.</div>
            </div>

            <?php if ($supportSettings->isTrialActive()): ?>
                <div class="uk-alert-primary" uk-alert>
                    Пробный Start действует до <?= Html::encode(date('d.m.Y H:i', strtotime((string)$supportSettings->planExpiresAt))) ?>.
                </div>
            <?php endif; ?>

            <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$js = <<<JS
let chat_bot_check = $('.field-users-modules-chatbots input[type="checkbox"]');
let big_data_div = $('.field-users-modules-bigdata');
big_data_div.addClass('uk-margin-left');
checkedChatBot(chat_bot_check);

chat_bot_check.on('change', function(){
    checkedChatBot(this);
});

function checkedChatBot(element) {
    let big_data_check = $('.field-users-modules-bigdata input[type="checkbox"]');
    if ($(element).is(':checked')){
       big_data_div.removeClass('uk-hidden');
    } else {
        big_data_div.addClass('uk-hidden');
        big_data_check.prop('checked', false);
    }
}
JS;
$this->registerJs($js);
