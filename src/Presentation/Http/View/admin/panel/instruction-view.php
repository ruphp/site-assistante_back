<?php

use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleFeedbackRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/**
 * @var InstructionArticleRecord $article
 * @var InstructionArticleFeedbackRecord[] $comments
 * @var Users|null $client
 */

$this->title = 'Просмотр инструкции';
$clientTitle = $client instanceof Users
    ? trim((string)($client->firm ?: $client->name ?: $client->email))
    : '';
?>

<div class="uk-container uk-margin">
    <div class="uk-margin">
        <?= Html::a('← К инструкциям клиентов', ['/admin/instructions'], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
    </div>

    <div class="sw-instruction-card uk-margin">
        <div class="sw-instruction-card__top">
            <div>
                <h3 class="uk-margin-remove-bottom"><?= Html::encode($article->title) ?></h3>
                <div class="uk-text-meta">
                    Public key: <?= Html::encode((string)$article->public_key) ?>
                    <?php if ($clientTitle !== ''): ?>
                        · Клиент: <?= Html::encode($clientTitle) ?>
                    <?php endif; ?>
                    · ID: <?= Html::encode((string)$article->id) ?>
                </div>
            </div>
            <span class="uk-label <?= $article->admin_blocked ? 'uk-label-danger' : '' ?>">
                <?= $article->admin_blocked ? 'заблокирована' : 'активна' ?>
            </span>
        </div>

        <div class="sw-instruction-summary uk-grid-small uk-child-width-1-4@m uk-margin-top" uk-grid>
            <div><div class="sw-instruction-stat"><b><?= Html::encode((string)$article->views) ?></b><span>просмотров</span></div></div>
            <div><div class="sw-instruction-stat"><b><?= Html::encode((string)$article->likes) ?></b><span>лайков</span></div></div>
            <div><div class="sw-instruction-stat"><b><?= Html::encode((string)$article->dislikes) ?></b><span>дизлайков</span></div></div>
            <div><div class="sw-instruction-stat"><b><?= round((int)$article->content_bytes / 1024, 1) ?> КБ</b><span>в базе</span></div></div>
        </div>

        <div class="uk-margin-top">
            <?= Html::a($article->admin_blocked ? 'Разблокировать' : 'Заблокировать', ['/admin/instructions/block', 'id' => $article->id], [
                'class' => 'uk-button uk-button-primary',
                'data' => ['method' => 'post'],
            ]) ?>
        </div>
    </div>

    <section class="sw-instruction-card uk-margin">
        <h4>Содержимое инструкции</h4>
        <div class="sw-instruction-preview">
            <?= HtmlPurifier::process((string)$article->html) ?>
        </div>
    </section>

    <section class="sw-instruction-card uk-margin">
        <h4>Последние отзывы</h4>
        <?php if ($comments === []): ?>
            <div class="uk-text-meta">Отзывов пока нет.</div>
        <?php else: ?>
            <div class="sw-instruction-comments">
                <?php foreach ($comments as $comment): ?>
                    <article class="sw-instruction-comment">
                        <div class="uk-text-meta">
                            Оценка: <?= $comment->is_like ? 'нравится' : 'не нравится' ?>
                            · посетитель <?= Html::encode((string)$comment->visitor_key) ?>
                        </div>
                        <?php if ((string)$comment->comment !== ''): ?>
                            <div><?= nl2br(Html::encode((string)$comment->comment)) ?></div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
