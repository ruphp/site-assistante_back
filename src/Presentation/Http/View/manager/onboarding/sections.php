<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSectionRecord;
use yii\helpers\Html;

/**
 * @var OnboardingRecord $onboarding
 * @var OnboardingSectionRecord[] $sections
 * @var OnboardingSectionRecord $editSection
 * @var bool $urlBindingsEnabled
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Разделы сценария';
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3>Разделы сценария: <?= Html::encode($onboarding->title) ?></h3>
    <div class="uk-text-meta uk-margin-small-bottom">Раздел привязан к странице. Если следующий раздел на другом URL, кнопка сценария переведет пользователя дальше.</div>

    <div class="sw-instruction-grid">
        <?php foreach ($sections as $section): ?>
            <article class="sw-instruction-card">
                <div class="sw-instruction-card__top">
                    <h4><?= Html::encode($section->title) ?></h4>
                    <span class="uk-label <?= $section->is_active ? '' : 'uk-label-warning' ?>"><?= $section->is_active ? 'включен' : 'выключен' ?></span>
                </div>
                <div class="uk-text-meta"><?= Html::encode($section->url ?: 'без URL') ?> · порядок <?= Html::encode((string)$section->sort_order) ?></div>
                <div class="sw-instruction-actions uk-margin-small-top">
                    <?= Html::a('Шаги', ['/manager/onboarding/steps', 'sectionId' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                    <?= Html::a('Редактировать', ['/manager/onboarding/sections', 'onboardingId' => $onboarding->id, 'editId' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                    <?= Html::a('Удалить', ['/manager/onboarding/section-delete', 'id' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small', 'data' => ['method' => 'post', 'confirm' => 'Удалить раздел и его шаги?']]) ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <h4 class="uk-margin-large-top"><?= $editSection->isNewRecord ? 'Новый раздел' : 'Редактировать раздел' ?></h4>
    <?= Html::beginForm('', 'post', ['class' => 'uk-form-stacked sw-form-panel']) ?>
        <?= $editSection->isNewRecord ? '' : Html::hiddenInput('OnboardingSection[id]', (string)$editSection->id) ?>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-3@m">
                <?= Html::label('Название', 'section-title', ['class' => 'uk-form-label']) ?>
                <?= Html::input('text', 'OnboardingSection[title]', (string)$editSection->title, ['id' => 'section-title', 'class' => 'uk-input', 'placeholder' => 'Например: корзина']) ?>
            </div>
            <div class="uk-width-1-3@m">
                <?= Html::label('URL страницы', 'section-url', ['class' => 'uk-form-label']) ?>
                <?= Html::input('text', 'OnboardingSection[url]', (string)$editSection->url, ['id' => 'section-url', 'class' => 'uk-input', 'placeholder' => '/cart']) ?>
                <?php if (!$urlBindingsEnabled): ?><div class="uk-text-meta">Расширенные URL-привязки доступны в Pro.</div><?php endif; ?>
            </div>
            <div class="uk-width-1-6@m">
                <?= Html::label('Порядок', 'section-order', ['class' => 'uk-form-label']) ?>
                <?= Html::input('number', 'OnboardingSection[sort_order]', (string)$editSection->sort_order, ['id' => 'section-order', 'class' => 'uk-input']) ?>
            </div>
            <div class="uk-width-1-6@m uk-flex uk-flex-middle uk-margin-top">
                <label><?= Html::checkbox('OnboardingSection[is_active]', (bool)$editSection->is_active) ?> Включен</label>
            </div>
        </div>
        <?php if ($urlBindingsEnabled): ?>
            <div class="uk-margin-small">
                <label><?= Html::checkbox('OnboardingSection[include_children]', (bool)$editSection->include_children) ?> включая дочерние URL</label>
                <label class="uk-margin-left"><?= Html::checkbox('OnboardingSection[include_query]', (bool)$editSection->include_query) ?> учитывать query string</label>
            </div>
        <?php endif; ?>
        <div class="uk-margin">
            <?= Html::submitButton($editSection->isNewRecord ? 'Добавить раздел' : 'Сохранить раздел', ['class' => 'uk-button uk-button-primary']) ?>
            <?php if (!$editSection->isNewRecord): ?>
                <?= Html::a('Отмена', ['/manager/onboarding/sections', 'onboardingId' => $onboarding->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default']) ?>
            <?php endif; ?>
        </div>
    <?= Html::endForm() ?>
</div>
