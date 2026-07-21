<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSectionRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingStepRecord;
use yii\helpers\Html;

/**
 * @var OnboardingRecord $onboarding
 * @var array $structure
 * @var ClientProjectView $activeProject
 */

$sections = $structure['sectionsByOnboarding'][(int)$onboarding->id] ?? [];
$stepsBySection = $structure['stepsBySection'] ?? [];
?>

<div class="sw-onboarding-structure">
    <div class="sw-onboarding-structure__head">
        <div>
            <b>Структура сценария</b>
            <div class="uk-text-meta">Разделы идут по страницам, внутри разделов выполняются шаги.</div>
        </div>
        <?= Html::a('Добавить раздел', ['/manager/onboarding/sections', 'onboardingId' => $onboarding->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
    </div>

    <?php if ($sections === []): ?>
        <div class="sw-onboarding-structure__empty">Разделов пока нет. Добавьте первый раздел, затем шаги внутри него.</div>
    <?php endif; ?>

    <?php foreach ($sections as $section): ?>
        <?php
        /** @var OnboardingSectionRecord $section */
        $steps = $stepsBySection[(int)$section->id] ?? [];
        ?>
        <section class="sw-onboarding-section">
            <div class="sw-onboarding-section__title">
                <div>
                    <span class="sw-onboarding-section__order"><?= Html::encode((string)$section->sort_order) ?></span>
                    <b><?= Html::encode($section->title ?: 'Раздел') ?></b>
                    <span class="uk-label <?= $section->is_active ? '' : 'uk-label-warning' ?>"><?= $section->is_active ? 'включен' : 'выключен' ?></span>
                </div>
                <div class="sw-instruction-actions">
                    <?= Html::a('Шаги', ['/manager/onboarding/steps', 'sectionId' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-primary uk-button-small']) ?>
                    <?= Html::a('Править раздел', ['/manager/onboarding/sections', 'onboardingId' => $onboarding->id, 'editId' => $section->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                </div>
            </div>
            <div class="uk-text-meta"><?= Html::encode($section->url ?: 'без URL') ?></div>

            <div class="sw-onboarding-step-list">
                <?php if ($steps === []): ?>
                    <div class="sw-onboarding-step sw-onboarding-step--empty">Шагов нет</div>
                <?php endif; ?>
                <?php foreach ($steps as $step): ?>
                    <?php /** @var OnboardingStepRecord $step */ ?>
                    <article class="sw-onboarding-step">
                        <div class="sw-onboarding-step__main">
                            <span class="sw-onboarding-step__order"><?= Html::encode((string)$step->sort_order) ?></span>
                            <div>
                                <b>Шаг <?= Html::encode((string)$step->sort_order) ?></b>
                                <div><?= Html::encode(mb_substr(trim(strip_tags((string)$step->text)), 0, 90) ?: 'без текста') ?></div>
                                <div class="uk-text-meta"><?= Html::encode($step->selector ?: 'селектор не указан') ?></div>
                            </div>
                        </div>
                        <div class="sw-instruction-actions">
                            <span class="uk-label <?= $step->is_active ? '' : 'uk-label-warning' ?>"><?= $step->is_active ? 'включен' : 'выключен' ?></span>
                            <?= Html::a('Править', ['/manager/onboarding/steps', 'sectionId' => $section->id, 'editId' => $step->id, 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
