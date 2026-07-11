<?php

use app\Application\Panel\Dto\ManagerOperatorView;
use app\Presentation\Http\Form\ManagerOperatorForm;
use app\Presentation\Http\Form\ManagerOwnerContactForm;
use yii\helpers\Html;

/**
 * @var ManagerOperatorView[] $operators
 * @var ManagerOperatorForm $form
 * @var ManagerOwnerContactForm $ownerForm
 * @var int $operatorLimit
 * @var array<int, string> $telegramCodes
 */

$this->title = 'Менеджеры';
$usedOperators = count($operators);
?>

<div class="uk-container uk-margin">
    <h3>Менеджеры онлайн-поддержки</h3>

    <div class="uk-alert-primary" uk-alert>
        <p>
            Лимит тарифа: <?= Html::encode((string)$operatorLimit) ?> менеджер(ов).
            Первый менеджер - владелец аккаунта.
            Telegram подключается по персональной ссылке менеджера.
        </p>
    </div>

    <div class="uk-card uk-card-default uk-card-body uk-margin">
        <h4>Владелец аккаунта</h4>
        <p class="uk-text-meta">
            Владелец считается первым менеджером. Эти контакты будут использоваться для общения и отображения в операторской части.
        </p>

        <?= Html::beginForm('/manager/operator/owner-contacts', 'post', ['class' => 'uk-form-stacked', 'enctype' => 'multipart/form-data']) ?>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-2@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($ownerForm, 'name', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($ownerForm, 'name', ['class' => 'uk-input']) ?>
                        <?= Html::error($ownerForm, 'name', ['class' => 'uk-text-danger']) ?>
                    </div>
                </div>
                <div class="uk-width-1-2@s">
                    <div class="uk-margin">
                        <label class="uk-form-label">Email для входа</label>
                        <input class="uk-input" value="<?= Html::encode(Yii::$app->user->identity->email ?? '') ?>" disabled>
                    </div>
                </div>
                <div class="uk-width-1-3@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($ownerForm, 'phone', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($ownerForm, 'phone', ['class' => 'uk-input']) ?>
                    </div>
                </div>
                <div class="uk-width-1-2@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($ownerForm, 'avatar', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeFileInput($ownerForm, 'avatar', ['class' => 'uk-input', 'accept' => 'image/png,image/jpeg,image/webp']) ?>
                        <?= Html::error($ownerForm, 'avatar', ['class' => 'uk-text-danger']) ?>
                    </div>
                </div>
                <div class="uk-width-1-3@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($ownerForm, 'telegram', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($ownerForm, 'telegram', ['class' => 'uk-input']) ?>
                    </div>
                </div>
                <div class="uk-width-1-3@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($ownerForm, 'maxContact', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($ownerForm, 'maxContact', ['class' => 'uk-input']) ?>
                    </div>
                </div>
            </div>

            <div class="uk-margin-top">
                <?= Html::submitButton('Сохранить контакты владельца', ['class' => 'uk-button uk-button-primary']) ?>
            </div>
        <?= Html::endForm() ?>
    </div>

    <table class="uk-table uk-table-divider uk-table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Телефон</th>
            <th>Telegram</th>
            <th>MAX</th>
            <th>Код Telegram-бота</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($operators as $operator): ?>
            <tr>
                <td><?= Html::encode((string)$operator->id) ?></td>
                <td>
                    <?php if ($operator->avatarUrl !== null): ?>
                        <?= Html::img($operator->avatarUrl, [
                            'alt' => '',
                            'style' => 'width:32px;height:32px;border-radius:50%;object-fit:cover;margin-right:8px;vertical-align:middle',
                        ]) ?>
                    <?php endif; ?>
                    <?= Html::encode($operator->name) ?>
                    <?php if ($operator->isOwner): ?>
                        <div class="uk-text-meta">Владелец</div>
                    <?php endif; ?>
                </td>
                <td><?= Html::encode($operator->email) ?></td>
                <td><?= Html::encode($operator->phone !== '' ? $operator->phone : '-') ?></td>
                <td><?= Html::encode($operator->telegram !== '' ? $operator->telegram : '-') ?></td>
                <td><?= Html::encode($operator->maxContact !== '' ? $operator->maxContact : '-') ?></td>
                <td>
                    <?php $telegramCode = $telegramCodes[$operator->id] ?? ''; ?>
                    <?php if ($telegramCode !== ''): ?>
                        <?= Html::a('Подключить Telegram', 'https://t.me/SiteWidgetBot?start=' . rawurlencode($telegramCode), [
                            'class' => 'uk-button uk-button-default uk-button-small',
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer',
                        ]) ?>
                        <div class="uk-text-meta">ссылка действует 30 минут</div>
                    <?php endif; ?>
                </td>
                <td class="uk-text-nowrap">
                    <?php if (!$operator->isOwner): ?>
                        <?= Html::beginForm('/manager/operator/reset-password', 'post', ['style' => 'display:inline']) ?>
                            <?= Html::hiddenInput('id', (string)$operator->id) ?>
                            <?= Html::submitButton('Сбросить пароль', [
                                'class' => 'uk-button uk-button-default uk-button-small',
                                'onclick' => "return confirm('Сгенерировать новый пароль менеджеру?');",
                            ]) ?>
                        <?= Html::endForm() ?>

                        <?= Html::beginForm('/manager/operator/disable', 'post', ['style' => 'display:inline']) ?>
                            <?= Html::hiddenInput('id', (string)$operator->id) ?>
                            <?= Html::submitButton('Отключить', [
                                'class' => 'uk-button uk-button-danger uk-button-small',
                                'onclick' => "return confirm('Отключить менеджера?');",
                            ]) ?>
                        <?= Html::endForm() ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($usedOperators >= $operatorLimit): ?>
        <div class="uk-alert-warning" uk-alert>
            <p>Лимит менеджеров исчерпан. Для расширения тарифа напишите нам через виджет.</p>
        </div>
    <?php else: ?>
        <h4>Добавить менеджера</h4>

        <?= Html::beginForm('/manager/operators', 'post', ['class' => 'uk-form-stacked', 'enctype' => 'multipart/form-data']) ?>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-2@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($form, 'name', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($form, 'name', ['class' => 'uk-input']) ?>
                        <?= Html::error($form, 'name', ['class' => 'uk-text-danger']) ?>
                    </div>
                </div>
                <div class="uk-width-1-2@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($form, 'email', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($form, 'email', ['class' => 'uk-input']) ?>
                        <?= Html::error($form, 'email', ['class' => 'uk-text-danger']) ?>
                    </div>
                </div>
                <div class="uk-width-1-3@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($form, 'phone', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($form, 'phone', ['class' => 'uk-input']) ?>
                    </div>
                </div>
                <div class="uk-width-1-2@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($form, 'avatar', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeFileInput($form, 'avatar', ['class' => 'uk-input', 'accept' => 'image/png,image/jpeg,image/webp']) ?>
                        <?= Html::error($form, 'avatar', ['class' => 'uk-text-danger']) ?>
                    </div>
                </div>
                <div class="uk-width-1-3@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($form, 'telegram', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($form, 'telegram', ['class' => 'uk-input']) ?>
                    </div>
                </div>
                <div class="uk-width-1-3@s">
                    <div class="uk-margin">
                        <?= Html::activeLabel($form, 'maxContact', ['class' => 'uk-form-label']) ?>
                        <?= Html::activeTextInput($form, 'maxContact', ['class' => 'uk-input']) ?>
                    </div>
                </div>
            </div>

            <div class="uk-text-meta uk-margin-small-bottom">
                Пароль создастся автоматически и появится на экране владельца.
            </div>

            <?= Html::submitButton('Создать менеджера', ['class' => 'uk-button uk-button-primary']) ?>
        <?= Html::endForm() ?>
    <?php endif; ?>
</div>
