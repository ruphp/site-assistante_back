<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleFeedbackRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
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
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3><?= Html::encode($article->title) ?></h3>
    <div class="sw-instruction-summary uk-grid-small uk-child-width-1-4@m" uk-grid>
        <div><div class="sw-instruction-stat"><b><?= Html::encode((string)$article->views) ?></b><span>просмотров</span></div></div>
        <div><div class="sw-instruction-stat"><b><?= Html::encode((string)$article->likes) ?></b><span>лайков</span></div></div>
        <div><div class="sw-instruction-stat"><b><?= Html::encode((string)$article->dislikes) ?></b><span>дизлайков</span></div></div>
        <div><div class="sw-instruction-stat"><b><?= Html::encode((string)count($comments)) ?></b><span>отзывов</span></div></div>
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
