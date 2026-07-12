<?php

use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionSettingsRecord;
use yii\helpers\Html;

/**
 * @var InstructionArticleRecord[] $articles
 * @var InstructionSettingsRecord[] $settings
 * @var int[] $clientPublicKeys
 * @var int $publicKey
 */

$this->title = 'Инструкции клиентов';
$filterPublicKey = (int)$publicKey;
$publicKeys = array_values(array_unique(array_merge(
    array_map('intval', $clientPublicKeys),
    array_map(static fn(InstructionArticleRecord $article): int => (int)$article->public_key, $articles),
)));
sort($publicKeys);
?>

<div class="uk-container uk-margin">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
        <div>
            <h3 class="uk-margin-remove">Инструкции клиентов</h3>
            <?php if ($filterPublicKey > 0): ?>
                <div class="uk-text-meta">Фильтр по public key <?= Html::encode((string)$filterPublicKey) ?></div>
            <?php endif; ?>
        </div>
        <?php if ($filterPublicKey > 0): ?>
            <?= Html::a('Показать всех', ['/admin/instructions'], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
        <?php endif; ?>
    </div>

    <div class="sw-instruction-list uk-margin">
        <?php foreach ($publicKeys as $clientPublicKey): ?>
            <?php $locked = isset($settings[$clientPublicKey]) && $settings[$clientPublicKey]->creation_locked; ?>
            <div class="sw-instruction-card">
                <div class="sw-instruction-card__top">
                    <h4>Public key <?= Html::encode((string)$clientPublicKey) ?></h4>
                    <div class="uk-text-nowrap">
                        <?= Html::a('Смотреть', ['/admin/instructions', 'publicKey' => $clientPublicKey], [
                            'class' => 'uk-button uk-button-default uk-button-small',
                        ]) ?>
                        <?= Html::a($locked ? 'Разрешить создание' : 'Запретить создание', ['/admin/instructions/creation', 'publicKey' => $clientPublicKey], [
                            'class' => 'uk-button uk-button-default uk-button-small',
                            'data' => ['method' => 'post'],
                        ]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="sw-instruction-grid">
        <?php foreach ($articles as $article): ?>
            <article class="sw-instruction-card">
                <div class="sw-instruction-card__top">
                    <h4><?= Html::encode($article->title) ?></h4>
                    <span class="uk-label <?= $article->admin_blocked ? 'uk-label-danger' : '' ?>"><?= $article->admin_blocked ? 'заблокирована' : 'активна' ?></span>
                </div>
                <div class="uk-text-meta">
                    Public key: <?= Html::encode((string)$article->public_key) ?> · Просмотров: <?= Html::encode((string)$article->views) ?> · <?= round((int)$article->content_bytes / 1024, 1) ?> КБ
                </div>
                <div class="uk-margin-small-top">
                    <?= Html::a('Просмотр', ['/admin/instructions/view', 'id' => $article->id], [
                        'class' => 'uk-button uk-button-default uk-button-small',
                    ]) ?>
                    <?= Html::a($article->admin_blocked ? 'Разблокировать' : 'Заблокировать', ['/admin/instructions/block', 'id' => $article->id], [
                        'class' => 'uk-button uk-button-primary uk-button-small',
                        'data' => ['method' => 'post'],
                    ]) ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
