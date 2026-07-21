<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSectionRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingStepRecord;
use yii\helpers\Html;

/**
 * @var OnboardingRecord $onboarding
 * @var OnboardingSectionRecord[] $sections
 * @var OnboardingSectionRecord $editSection
 * @var OnboardingStepRecord[] $sectionSteps
 * @var bool $urlBindingsEnabled
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Разделы сценария';
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', [
        'projects' => $projects,
        'activeProject' => $activeProject,
        'projectTabsPath' => '/manager/onboarding',
    ]) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3>Разделы сценария: <?= Html::encode($onboarding->title) ?></h3>
    <div class="uk-text-meta uk-margin-small-bottom">Раздел привязан к странице. Если следующий раздел на другом URL, кнопка сценария переведет пользователя дальше.</div>

    <?php if ($editSection->isNewRecord): ?>
        <div class="sw-instruction-grid">
            <?php foreach ($sections as $section): ?>
                <article class="sw-instruction-card">
                    <div class="sw-instruction-card__top">
                        <h4><?= Html::encode($section->title) ?></h4>
                        <span class="uk-label <?= $section->is_active ? '' : 'uk-label-warning' ?>"><?= $section->is_active ? 'включен' : 'выключен' ?></span>
                    </div>
                    <div class="uk-text-meta"><?= Html::encode($section->url ?: 'без URL') ?> · порядок <?= Html::encode((string)$section->sort_order) ?></div>
                    <div class="sw-instruction-actions uk-margin-small-top">
                        <?= Html::a('Открыть раздел', ['/manager/onboarding/sections', 'onboardingId' => $onboarding->id, 'editId' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                        <?= Html::a('Шаги', ['/manager/onboarding/steps', 'sectionId' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        <?= Html::a('Удалить', ['/manager/onboarding/section-delete', 'id' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small', 'data' => ['method' => 'post', 'confirm' => 'Удалить раздел и его шаги?']]) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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

    <?php if (!$editSection->isNewRecord): ?>
        <section class="sw-instruction-card uk-margin-top">
            <div class="sw-instruction-card__top">
                <div>
                    <h4>Шаги раздела</h4>
                    <div class="uk-text-meta">Показываются только шаги раздела «<?= Html::encode($editSection->title) ?>».</div>
                </div>
                <?= Html::a('Добавить шаг', ['/manager/onboarding/steps', 'sectionId' => $editSection->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
            </div>

            <div class="sw-onboarding-step-list">
                <?php if ($sectionSteps === []): ?>
                    <div class="sw-onboarding-step sw-onboarding-step--empty">Шагов пока нет.</div>
                <?php endif; ?>
                <?php foreach ($sectionSteps as $step): ?>
                    <article class="sw-onboarding-step">
                        <div class="sw-onboarding-step__main">
                            <span class="sw-onboarding-step__order"><?= Html::encode((string)$step->sort_order) ?></span>
                            <div>
                                <b>Шаг <?= Html::encode((string)$step->sort_order) ?></b>
                                <div><?= Html::encode(mb_substr(trim(strip_tags((string)$step->text)), 0, 120) ?: 'без текста') ?></div>
                                <div class="uk-text-meta"><?= Html::encode($step->selector ?: 'селектор не указан') ?></div>
                            </div>
                        </div>
                        <div class="sw-instruction-actions">
                            <span class="uk-label <?= $step->is_active ? '' : 'uk-label-warning' ?>"><?= $step->is_active ? 'включен' : 'выключен' ?></span>
                            <?= Html::a('Править', ['/manager/onboarding/steps', 'sectionId' => $editSection->id, 'editId' => $step->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                            <?= Html::a('Удалить', ['/manager/onboarding/step-delete', 'id' => $step->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small', 'data' => ['method' => 'post', 'confirm' => 'Удалить шаг?']]) ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
