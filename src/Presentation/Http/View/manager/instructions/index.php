<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionCategoryRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var InstructionArticleRecord[] $articles
 * @var InstructionCategoryRecord[] $categories
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 * @var int $totalViews
 * @var int $totalLikes
 * @var int $storageBytes
 */

$this->title = 'Инструкции';
$categoryOptions = ['' => 'Все разделы'] + ArrayHelper::map($categories, 'id', 'name');
$statusOptions = ['' => 'Любой статус', 'active' => 'Включены', 'disabled' => 'Выключены'];
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin">
        <div>
            <h3 class="uk-margin-remove">Инструкции</h3>
            <div class="uk-text-meta">Public key проекта: <?= Html::encode((string)$activeProject->publicKey) ?></div>
        </div>
        <?= Html::a('Создать инструкцию', ['/manager/instructions/create', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary']) ?>
    </div>

    <div class="sw-instruction-summary uk-grid-small uk-child-width-1-4@m" uk-grid>
        <div><div class="sw-instruction-stat"><b><?= Html::encode((string)count($articles)) ?></b><span>инструкций</span></div></div>
        <div><div class="sw-instruction-stat"><b><?= Html::encode((string)$totalViews) ?></b><span>просмотров</span></div></div>
        <div><div class="sw-instruction-stat"><b><?= Html::encode((string)$totalComments) ?></b><span>отзывов</span></div></div>
        <div><div class="sw-instruction-stat"><b><?= round($storageBytes / 1024, 1) ?> КБ</b><span>занято в базе</span></div></div>
    </div>

    <?= Html::beginForm(['/manager/instructions', 'projectId' => $activeProject->id], 'get', ['class' => 'uk-grid-small uk-margin', 'uk-grid' => true]) ?>
        <input type="hidden" name="projectId" value="<?= Html::encode((string)$activeProject->id) ?>">
        <div class="uk-width-1-3@m">
            <?= Html::dropDownList('categoryId', Yii::$app->request->get('categoryId'), $categoryOptions, ['class' => 'uk-select']) ?>
        </div>
        <div class="uk-width-1-3@m">
            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $statusOptions, ['class' => 'uk-select']) ?>
        </div>
        <div class="uk-width-auto">
            <button class="uk-button uk-button-default" type="submit">Показать</button>
        </div>
    <?= Html::endForm() ?>

    <div class="sw-instruction-grid">
        <?php foreach ($articles as $article): ?>
            <article class="sw-instruction-card">
                <div class="sw-instruction-card__top">
                    <h4><?= Html::encode($article->title) ?></h4>
                    <span class="uk-label <?= $article->is_active ? '' : 'uk-label-warning' ?>"><?= $article->is_active ? 'включена' : 'выключена' ?></span>
                </div>
                <div class="uk-text-meta">
                    Просмотров: <?= Html::encode((string)$article->views) ?> · Лайков: <?= Html::encode((string)$article->likes) ?> · <?= round((int)$article->content_bytes / 1024, 1) ?> КБ
                </div>
                <div class="sw-instruction-actions uk-margin-small-top">
                    <?= Html::a('Редактировать', ['/manager/instructions/update', 'id' => $article->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                    <?= Html::a('Статистика', ['/manager/instructions/stats', 'id' => $article->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                    <?= Html::a('Удалить', ['/manager/instructions/article-delete', 'id' => $article->id, 'projectId' => $activeProject->id], [
                        'class' => 'uk-button uk-button-default uk-button-small',
                        'data' => ['method' => 'post', 'confirm' => 'Удалить инструкцию?'],
                    ]) ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
