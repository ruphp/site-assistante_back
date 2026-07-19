<?php

use app\Presentation\Http\Form\ManagerOwnerContactForm;
use yii\helpers\Html;

/** @var ManagerOwnerContactForm $form */
/** @var \app\Infrastructure\YiiActiveRecord\Users $accountUser */
/** @var string|null $avatarUrl */
/** @var string $telegramCode */

$this->title = 'Моя карточка менеджера';
?>
<div class="uk-container uk-margin">
    <h3>Личный кабинет</h3>

    <div class="uk-card uk-card-default uk-card-body uk-margin">
        <h4>Данные аккаунта</h4>
        <p class="uk-text-meta">Эти данные указаны при регистрации. Email является точкой привязки аккаунта и меняется только по запросу.</p>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-3@s">
                <label class="uk-form-label">ФИО / имя владельца</label>
                <input class="uk-input" value="<?= Html::encode((string)$accountUser->name) ?>" disabled>
            </div>
            <div class="uk-width-1-3@s">
                <label class="uk-form-label">Email</label>
                <input class="uk-input" value="<?= Html::encode((string)$accountUser->email) ?>" disabled>
            </div>
            <div class="uk-width-1-3@s">
                <label class="uk-form-label">Организация/сервис</label>
                <input class="uk-input" value="<?= Html::encode((string)$accountUser->firm) ?>" disabled>
            </div>
        </div>
    </div>

    <div class="uk-card uk-card-default uk-card-body uk-margin">
        <h4>Карточка менеджера для обращений</h4>
        <?= Html::beginForm('/manager/profile', 'post', ['class' => 'uk-form-stacked', 'enctype' => 'multipart/form-data']) ?>
        <?php if ($avatarUrl !== null): ?>
            <div class="uk-margin">
                <?= Html::img($avatarUrl, ['alt' => '', 'style' => 'width:80px;height:80px;border-radius:50%;object-fit:cover']) ?>
            </div>
        <?php endif; ?>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-2@s">
                <?= Html::activeLabel($form, 'name', ['class' => 'uk-form-label']) ?>
                <?= Html::activeTextInput($form, 'name', ['class' => 'uk-input']) ?>
                <?= Html::error($form, 'name', ['class' => 'uk-text-danger']) ?>
            </div>
            <div class="uk-width-1-2@s">
                <?= Html::activeLabel($form, 'avatar', ['class' => 'uk-form-label']) ?>
                <?= Html::activeFileInput($form, 'avatar', ['class' => 'uk-input', 'accept' => 'image/png,image/jpeg,image/webp']) ?>
                <?= Html::error($form, 'avatar', ['class' => 'uk-text-danger']) ?>
            </div>
            <div class="uk-width-1-3@s">
                <?= Html::activeLabel($form, 'phone', ['class' => 'uk-form-label']) ?>
                <?= Html::activeTextInput($form, 'phone', ['class' => 'uk-input']) ?>
            </div>
            <div class="uk-width-1-3@s">
                <?= Html::activeLabel($form, 'telegram', ['class' => 'uk-form-label']) ?>
                <?= Html::activeTextInput($form, 'telegram', ['class' => 'uk-input']) ?>
            </div>
            <div class="uk-width-1-3@s">
                <?= Html::activeLabel($form, 'maxContact', ['class' => 'uk-form-label']) ?>
                <?= Html::activeTextInput($form, 'maxContact', ['class' => 'uk-input']) ?>
            </div>
        </div>
        <div class="uk-margin-top">
            <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>
        </div>
        <?= Html::endForm() ?>
    </div>

    <div class="uk-card uk-card-default uk-card-body uk-margin">
        <h4>Telegram</h4>
        <p>Подключите бота, чтобы получать обращения и отвечать из Telegram.</p>
        <?= Html::a('Подключить @SiteWidgetBot', 'https://t.me/SiteWidgetBot?start=' . rawurlencode($telegramCode), [
            'class' => 'uk-button uk-button-default',
            'target' => '_blank',
            'rel' => 'noopener noreferrer',
        ]) ?>
        <div class="uk-text-meta uk-margin-small-top">Ссылка действует 30 минут.</div>
    </div>
</div>
