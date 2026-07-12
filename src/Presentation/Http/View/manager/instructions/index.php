<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionCategoryRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var InstructionCategoryRecord[] $categories
 * @var InstructionArticleRecord[] $articles
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Инструкции';
$categoryOptions = ['' => 'Без категории'] + ArrayHelper::map($categories, 'id', 'name');
$indexUrl = Url::to(['/manager/instructions', 'projectId' => $activeProject->id]);
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>

    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin">
        <div>
            <h3 class="uk-margin-remove">Инструкции виджета</h3>
            <div class="uk-text-meta">
                Разделы и материалы, которые посетитель увидит внутри модуля «Инструкции».
            </div>
        </div>
    </div>

    <div class="sw-instruction-layout">
        <section class="sw-instruction-panel">
            <h4>Разделы</h4>

            <div class="sw-instruction-card sw-instruction-card--new">
                <h5>Новый раздел</h5>
                <?= Html::beginForm($indexUrl, 'post', ['class' => 'uk-form-stacked']) ?>
                    <input type="hidden" name="InstructionCategory[id]" value="">
                    <div class="uk-margin-small">
                        <label class="uk-form-label">Название</label>
                        <input class="uk-input" name="InstructionCategory[name]" placeholder="Например: Оплата и доставка">
                    </div>
                    <div class="uk-margin-small">
                        <label class="uk-form-label">Родительский раздел</label>
                        <?= Html::dropDownList('InstructionCategory[parent_id]', '', $categoryOptions, ['class' => 'uk-select']) ?>
                    </div>
                    <div class="sw-instruction-card__footer">
                        <label class="uk-flex uk-flex-middle uk-margin-remove">
                            <input class="uk-checkbox uk-margin-small-right" type="checkbox" name="InstructionCategory[is_active]" value="1" checked>
                            Включен
                        </label>
                        <input class="uk-input sw-instruction-sort" type="number" name="InstructionCategory[sort_order]" value="100" aria-label="Порядок">
                        <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>
                    </div>
                <?= Html::endForm() ?>
            </div>

            <div class="sw-instruction-list">
                <?php foreach ($categories as $category): ?>
                    <div class="sw-instruction-card">
                        <?= Html::beginForm($indexUrl, 'post', ['class' => 'uk-form-stacked']) ?>
                            <input type="hidden" name="InstructionCategory[id]" value="<?= Html::encode((string)$category->id) ?>">
                            <div class="uk-margin-small">
                                <label class="uk-form-label">Название</label>
                                <input class="uk-input" name="InstructionCategory[name]" value="<?= Html::encode($category->name) ?>">
                            </div>
                            <div class="uk-margin-small">
                                <label class="uk-form-label">Родительский раздел</label>
                                <?php
                                $currentCategoryOptions = $categoryOptions;
                                unset($currentCategoryOptions[$category->id]);
                                ?>
                                <?= Html::dropDownList('InstructionCategory[parent_id]', $category->parent_id, $currentCategoryOptions, ['class' => 'uk-select']) ?>
                            </div>
                            <div class="sw-instruction-card__footer">
                                <label class="uk-flex uk-flex-middle uk-margin-remove">
                                    <input class="uk-checkbox uk-margin-small-right" type="checkbox" name="InstructionCategory[is_active]" value="1" <?= $category->is_active ? 'checked' : '' ?>>
                                    Включен
                                </label>
                                <input class="uk-input sw-instruction-sort" type="number" name="InstructionCategory[sort_order]" value="<?= Html::encode((string)$category->sort_order) ?>" aria-label="Порядок">
                                <div class="sw-instruction-actions">
                                    <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                                    <?= Html::a('Удалить', Url::to(['/manager/instructions/category-delete', 'id' => $category->id, 'projectId' => $activeProject->id]), [
                                        'class' => 'uk-button uk-button-default uk-button-small',
                                        'data' => [
                                            'method' => 'post',
                                            'confirm' => 'Удалить раздел?',
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                        <?= Html::endForm() ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="sw-instruction-panel">
            <h4>Материалы</h4>

            <div class="sw-instruction-card sw-instruction-card--new">
                <h5>Новая инструкция</h5>
                <?= Html::beginForm($indexUrl, 'post', ['class' => 'uk-form-stacked']) ?>
                    <input type="hidden" name="InstructionArticle[id]" value="">
                    <div class="uk-margin-small">
                        <label class="uk-form-label">Заголовок</label>
                        <input class="uk-input" name="InstructionArticle[title]" placeholder="Например: Как оформить заказ">
                    </div>
                    <div class="uk-margin-small">
                        <label class="uk-form-label">Раздел</label>
                        <?= Html::dropDownList('InstructionArticle[category_id]', '', $categoryOptions, ['class' => 'uk-select']) ?>
                    </div>
                    <div class="uk-margin-small">
                        <label class="uk-form-label">Текст инструкции</label>
                        <textarea class="uk-textarea" name="InstructionArticle[html]" rows="8" placeholder="Можно вставлять простой HTML: ссылки, списки, изображения."></textarea>
                    </div>
                    <div class="sw-instruction-card__footer">
                        <label class="uk-flex uk-flex-middle uk-margin-remove">
                            <input class="uk-checkbox uk-margin-small-right" type="checkbox" name="InstructionArticle[is_active]" value="1" checked>
                            Включена
                        </label>
                        <input class="uk-input sw-instruction-sort" type="number" name="InstructionArticle[sort_order]" value="100" aria-label="Порядок">
                        <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']) ?>
                    </div>
                <?= Html::endForm() ?>
            </div>

            <div class="sw-instruction-grid">
                <?php foreach ($articles as $article): ?>
                    <div class="sw-instruction-card">
                        <?= Html::beginForm($indexUrl, 'post', ['class' => 'uk-form-stacked']) ?>
                            <input type="hidden" name="InstructionArticle[id]" value="<?= Html::encode((string)$article->id) ?>">
                            <div class="uk-margin-small">
                                <label class="uk-form-label">Заголовок</label>
                                <input class="uk-input" name="InstructionArticle[title]" value="<?= Html::encode($article->title) ?>">
                            </div>
                            <div class="uk-margin-small">
                                <label class="uk-form-label">Раздел</label>
                                <?= Html::dropDownList('InstructionArticle[category_id]', $article->category_id, $categoryOptions, ['class' => 'uk-select']) ?>
                            </div>
                            <div class="uk-margin-small">
                                <label class="uk-form-label">Текст инструкции</label>
                                <textarea class="uk-textarea" name="InstructionArticle[html]" rows="8"><?= Html::encode($article->html) ?></textarea>
                            </div>
                            <div class="sw-instruction-card__footer">
                                <label class="uk-flex uk-flex-middle uk-margin-remove">
                                    <input class="uk-checkbox uk-margin-small-right" type="checkbox" name="InstructionArticle[is_active]" value="1" <?= $article->is_active ? 'checked' : '' ?>>
                                    Включена
                                </label>
                                <input class="uk-input sw-instruction-sort" type="number" name="InstructionArticle[sort_order]" value="<?= Html::encode((string)$article->sort_order) ?>" aria-label="Порядок">
                                <div class="sw-instruction-actions">
                                    <?= Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                                    <?= Html::a('Удалить', Url::to(['/manager/instructions/article-delete', 'id' => $article->id, 'projectId' => $activeProject->id]), [
                                        'class' => 'uk-button uk-button-default uk-button-small',
                                        'data' => [
                                            'method' => 'post',
                                            'confirm' => 'Удалить инструкцию?',
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                        <?= Html::endForm() ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>
