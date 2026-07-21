<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleFeedbackRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Presentation\Http\View\Helper\RussianPlural;
use yii\helpers\Html;

/**
 * @var InstructionArticleRecord $article
 * @var InstructionArticleFeedbackRecord[] $comments
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Статистика инструкции';
?>

<div class="uk-container uk-margin">
    <div class="uk-margin">
        <?= Html::a('← К инструкциям', ['/manager/instructions', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
    </div>

    <h3 class="uk-margin-remove-bottom">Статистика инструкции</h3>
    <div class="uk-text-meta uk-margin-small-bottom"><?= Html::encode($article->title) ?></div>
    <div class="sw-module-summary sw-instruction-summary">
        <div class="sw-module-stat sw-instruction-stat"><b><?= Html::encode((string)$article->views) ?></b><span><?= Html::encode(RussianPlural::word((int)$article->views, 'просмотр', 'просмотра', 'просмотров')) ?></span></div>
        <div class="sw-module-stat sw-instruction-stat"><b><?= Html::encode((string)$article->likes) ?></b><span><?= Html::encode(RussianPlural::word((int)$article->likes, 'лайк', 'лайка', 'лайков')) ?></span></div>
        <div class="sw-module-stat sw-instruction-stat"><b><?= Html::encode((string)$article->dislikes) ?></b><span><?= Html::encode(RussianPlural::word((int)$article->dislikes, 'дизлайк', 'дизлайка', 'дизлайков')) ?></span></div>
        <div class="sw-module-stat sw-instruction-stat"><b><?= Html::encode((string)count($comments)) ?></b><span><?= Html::encode(RussianPlural::word(count($comments), 'отзыв', 'отзыва', 'отзывов')) ?></span></div>
    </div>

    <section class="sw-instruction-card uk-margin-top">
        <h4>Последние отзывы</h4>
        <?php if ($comments === []): ?>
            <div class="uk-text-meta">Отзывов пока нет.</div>
        <?php else: ?>
            <div class="sw-instruction-comments">
                <?php foreach ($comments as $comment): ?>
                    <article class="sw-instruction-comment">
                        <div class="uk-text-meta">
                            Оценка: <?= $comment->is_like ? 'нравится' : 'не нравится' ?> · посетитель <?= Html::encode((string)$comment->visitor_key) ?>
                        </div>
                        <div><?= nl2br(Html::encode($comment->comment)) ?></div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
