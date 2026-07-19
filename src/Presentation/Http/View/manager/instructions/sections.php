<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionCategoryRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var InstructionCategoryRecord[] $categories
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Разделы инструкций';
$categoryOptions = ['' => 'Без родительского раздела'] + ArrayHelper::map($categories, 'id', 'name');
$formUrl = Url::to(['/manager/instructions/sections', 'projectId' => $activeProject->id]);
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3>Разделы инструкций</h3>

    <section class="sw-instruction-card sw-instruction-card--new uk-margin">
        <h4>Новый раздел</h4>
        <?= Html::beginForm($formUrl, 'post', ['class' => 'uk-form-stacked']) ?>
            <input type="hidden" name="InstructionCategory[id]" value="">
            <div class="uk-grid-small" uk-grid>
                <div class="uk-width-1-3@m">
                    <label class="uk-form-label">Название</label>
                    <input class="uk-input" name="InstructionCategory[name]" placeholder="Например: Оплата">
                </div>
                <div class="uk-width-1-3@m">
                    <label class="uk-form-label">Родительский раздел</label>
                    <?= Html::dropDownList('InstructionCategory[parent_id]', '', $categoryOptions, ['class' => 'uk-select']) ?>
                </div>
                <div class="uk-width-1-6@m">
                    <label class="uk-form-label">Порядок</label>
                    <input class="uk-input" type="number" name="InstructionCategory[sort_order]" value="100">
                </div>
                <div class="uk-width-1-6@m uk-flex uk-flex-bottom">
                    <label><input class="uk-checkbox" type="checkbox" name="InstructionCategory[is_active]" value="1" checked> Включен</label>
                </div>
            </div>
            <button class="uk-button uk-button-primary uk-margin-small-top" type="submit">Сохранить</button>
        <?= Html::endForm() ?>
    </section>

    <div class="sw-instruction-list">
        <?php foreach ($categories as $category): ?>
            <div class="sw-instruction-card">
                <?= Html::beginForm($formUrl, 'post', ['class' => 'uk-form-stacked']) ?>
                    <input type="hidden" name="InstructionCategory[id]" value="<?= Html::encode((string)$category->id) ?>">
                    <div class="uk-grid-small" uk-grid>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label">Название</label>
                            <input class="uk-input" name="InstructionCategory[name]" value="<?= Html::encode($category->name) ?>">
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label">Родительский раздел</label>
                            <?php $options = $categoryOptions; unset($options[$category->id]); ?>
                            <?= Html::dropDownList('InstructionCategory[parent_id]', $category->parent_id, $options, ['class' => 'uk-select']) ?>
                        </div>
                        <div class="uk-width-1-6@m">
                            <label class="uk-form-label">Порядок</label>
                            <input class="uk-input" type="number" name="InstructionCategory[sort_order]" value="<?= Html::encode((string)$category->sort_order) ?>">
                        </div>
                        <div class="uk-width-1-6@m uk-flex uk-flex-bottom">
                            <label><input class="uk-checkbox" type="checkbox" name="InstructionCategory[is_active]" value="1" <?= $category->is_active ? 'checked' : '' ?>> Включен</label>
                        </div>
                    </div>
                    <div class="uk-margin-small-top">
                        <button class="uk-button uk-button-primary uk-button-small" type="submit">Сохранить</button>
                        <?= Html::a('Удалить', ['/manager/instructions/category-delete', 'id' => $category->id, 'projectId' => $activeProject->id], [
                            'class' => 'uk-button uk-button-default uk-button-small',
                            'data' => ['method' => 'post', 'confirm' => 'Удалить раздел можно только если в нем нет инструкций. Продолжить?'],
                        ]) ?>
                    </div>
                <?= Html::endForm() ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
