<?php

use app\Application\Panel\Dto\ClientProjectView;
use yii\helpers\Html;

/**
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 * @var array $settings
 * @var string|null $newToken
 * @var string|null $newCallbackSecret
 */

$this->title = 'SMS-коды';
$projectId = $activeProject->id;
$endpoint = rtrim(Yii::$app->request->hostInfo, '/') . '/api/sms/v1/tasks';
?>

<div class="uk-container uk-container-large uk-margin-top uk-margin-bottom">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', [
        'projects' => $projects,
        'activeProject' => $activeProject,
        'projectLimit' => $projectLimit,
        'projectTabsPath' => '/manager/sms-delivery',
    ]) ?>

    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
        <div>
            <h1 class="uk-heading-small uk-margin-remove-bottom">SMS-коды</h1>
            <p class="uk-text-meta uk-margin-small-top">Ручная отправка кодов через Android-приложение SiteWidget.</p>
        </div>
        <span class="uk-label <?= $settings['enabled'] ? 'uk-label-success' : '' ?>">
            <?= $settings['enabled'] ? 'Включено' : 'Выключено' ?>
        </span>
    </div>

    <?php if ($newToken): ?>
        <div class="uk-alert-primary" uk-alert>
            <strong>API-токен показывается один раз</strong>
            <div class="uk-margin-small-top"><code style="word-break: break-all"><?= Html::encode($newToken) ?></code></div>
            <?php if ($newCallbackSecret): ?>
                <div class="uk-margin-small-top">Секрет подписи callback:</div>
                <code style="word-break: break-all"><?= Html::encode($newCallbackSecret) ?></code>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="uk-grid-small uk-child-width-1-2@m" uk-grid>
        <section>
            <h2 class="uk-h3">Доступ проекта</h2>
            <p>Токен: <?= $settings['configured'] ? Html::encode($settings['tokenPrefix']) . '...' : 'ещё не создан' ?></p>
            <?= Html::beginForm(['/manager/sms-delivery/token', 'projectId' => $projectId], 'post') ?>
                <?= Html::submitButton($settings['configured'] ? 'Создать новый токен' : 'Создать токен', [
                    'class' => 'uk-button uk-button-primary',
                    'data-confirm' => $settings['configured'] ? 'Старый токен сразу перестанет работать. Продолжить?' : null,
                ]) ?>
            <?= Html::endForm() ?>

            <?php if ($settings['configured']): ?>
                <?= Html::beginForm(['/manager/sms-delivery/save', 'projectId' => $projectId], 'post', ['class' => 'uk-form-stacked uk-margin-large-top']) ?>
                    <div class="uk-margin">
                        <label><input class="uk-checkbox" type="checkbox" name="enabled" value="1" <?= $settings['enabled'] ? 'checked' : '' ?>> Принимать новые задания</label>
                    </div>
                    <div class="uk-margin">
                        <?= Html::label('Callback URL статусов', 'sms-callback-url', ['class' => 'uk-form-label']) ?>
                        <?= Html::input('url', 'callbackUrl', $settings['callbackUrl'], [
                            'id' => 'sms-callback-url',
                            'class' => 'uk-input',
                            'placeholder' => 'https://example.ru/api/sitewidget/sms-status',
                        ]) ?>
                        <div class="uk-text-meta uk-margin-small-top">Необязательно. SiteWidget отправит подписанные статусы notified, sms_opened и expired.</div>
                    </div>
                    <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </section>

        <section>
            <h2 class="uk-h3">Подключение</h2>
            <p>Сервер проекта отправляет JSON на:</p>
            <pre style="white-space: pre-wrap; word-break: break-all"><?= Html::encode($endpoint) ?></pre>
            <pre style="white-space: pre-wrap; word-break: break-word">{
  "requestId": "login-42-1730000000",
  "phone": "+79991234567",
  "text": "Код подтверждения: 1234",
  "expiresAt": "<?= Html::encode((new DateTimeImmutable('+5 minutes'))->format(DATE_ATOM)) ?>"
}</pre>
            <p class="uk-text-meta">Заголовок: <code>Authorization: Bearer ВАШ_ТОКЕН</code>. Повторный requestId не создаёт второе задание.</p>
        </section>
    </div>

    <?php if ($settings['counts'] !== []): ?>
        <div class="uk-margin-large-top">
            <h2 class="uk-h3">Статистика заданий</h2>
            <div class="uk-flex uk-flex-wrap" style="gap: 20px">
                <?php foreach ($settings['counts'] as $status => $count): ?>
                    <span><strong><?= Html::encode((string)$count) ?></strong> <?= Html::encode((string)$status) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
