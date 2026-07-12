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
 * @var bool $canCreateOperators
 */

$this->title = 'Менеджеры';
$usedOperators = count($operators);
$canAddOperator = $canCreateOperators && $usedOperators < $operatorLimit;
$currentUserId = (int)Yii::$app->user->id;
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

    <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
        <?php foreach ($operators as $operator): ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-top uk-margin-small-bottom">
                        <div class="uk-flex uk-flex-middle">
                            <?php if ($operator->avatarUrl !== null): ?>
                                <?= Html::img($operator->avatarUrl, [
                                    'alt' => '',
                                    'style' => 'width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px',
                                ]) ?>
                            <?php endif; ?>
                            <div>
                                <div class="uk-text-meta">ID <?= Html::encode((string)$operator->id) ?></div>
                                <h4 class="uk-margin-remove"><?= Html::encode($operator->name) ?></h4>
                                <?php if ($operator->isOwner): ?>
                                    <div class="uk-text-meta">Владелец</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="uk-grid-small uk-child-width-1-2@s" uk-grid>
                        <div>
                            <div class="uk-text-meta">Email</div>
                            <div><?= Html::encode($operator->email) ?></div>
                        </div>
                        <div>
                            <div class="uk-text-meta">Телефон</div>
                            <div><?= Html::encode($operator->phone !== '' ? $operator->phone : '-') ?></div>
                        </div>
                        <div>
                            <div class="uk-text-meta">Telegram</div>
                            <div><?= Html::encode($operator->telegram !== '' ? $operator->telegram : '-') ?></div>
                        </div>
                        <div>
                            <div class="uk-text-meta">MAX</div>
                            <div><?= Html::encode($operator->maxContact !== '' ? $operator->maxContact : '-') ?></div>
                        </div>
                    </div>

                    <div class="uk-margin-small-top">
                        <?php $telegramCode = $telegramCodes[$operator->id] ?? ''; ?>
                        <?php if ($operator->id === $currentUserId && $telegramCode !== ''): ?>
                            <?= Html::a('Подключить Telegram', 'https://t.me/SiteWidgetBot?start=' . rawurlencode($telegramCode), [
                                'class' => 'uk-button uk-button-default uk-button-small',
                                'target' => '_blank',
                                'rel' => 'noopener noreferrer',
                            ]) ?>
                            <div class="uk-text-meta">ссылка действует 30 минут</div>
                        <?php endif; ?>
                    </div>

                    <?php if (!$operator->isOwner): ?>
                        <div class="uk-margin-small-top uk-flex uk-flex-wrap uk-grid-small" uk-grid>
                            <div>
                                <?= Html::beginForm('/manager/operator/reset-password', 'post', ['style' => 'display:inline']) ?>
                                    <?= Html::hiddenInput('id', (string)$operator->id) ?>
                                    <?= Html::submitButton('Сбросить пароль', [
                                        'class' => 'uk-button uk-button-default uk-button-small',
                                        'onclick' => "return confirm('Сгенерировать новый пароль менеджеру?');",
                                    ]) ?>
                                <?= Html::endForm() ?>
                            </div>
                            <div>
                            <?= Html::beginForm('/manager/operator/disable', 'post', ['style' => 'display:inline']) ?>
                                <?= Html::hiddenInput('id', (string)$operator->id) ?>
                                <?= Html::submitButton('Отключить', [
                                    'class' => 'uk-button uk-button-danger uk-button-small',
                                    'onclick' => "return confirm('Отключить менеджера?');",
                                ]) ?>
                            <?= Html::endForm() ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div>
            <a class="uk-card uk-card-default uk-card-body uk-display-block uk-text-center" href="<?= $canAddOperator ? '#modal-operator-create' : '#modal-operator-limit' ?>" uk-toggle>
                <span uk-icon="icon: plus; ratio: 2"></span>
                <div class="uk-margin-small-top">Добавить менеджера</div>
            </a>
        </div>
    </div>

    <?php if (!$canCreateOperators || $usedOperators >= $operatorLimit): ?>
        <div class="uk-alert-warning" uk-alert>
            <p>Добавление менеджеров доступно на платном тарифе. На бесплатном тарифе доступен один менеджер — владелец аккаунта.</p>
        </div>
        <div id="modal-operator-limit" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <h3 class="uk-modal-title">Дополнительные менеджеры</h3>
                <p>Чтобы добавить ещё менеджеров онлайн-поддержки, перейдите на платный тариф.</p>
                <div class="uk-text-right">
                    <button class="uk-button uk-button-default uk-modal-close" type="button">Закрыть</button>
                    <?= Html::a('Написать в виджет', '/', ['class' => 'uk-button uk-button-primary']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($canAddOperator): ?>
        <div id="modal-operator-create" uk-modal>
            <div class="uk-modal-dialog uk-modal-body">
                <h3 class="uk-modal-title">Новый менеджер</h3>

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

                    <div class="uk-text-right">
                        <button class="uk-button uk-button-default uk-modal-close" type="button">Отмена</button>
                        <?= Html::submitButton('Создать менеджера', ['class' => 'uk-button uk-button-primary']) ?>
                    </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    <?php endif; ?>
</div>
