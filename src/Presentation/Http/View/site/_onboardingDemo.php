<?php

use yii\helpers\Html;

/** @var array $demoOnboardings */
/** @var bool $arrival */
$one = $demoOnboardings['Подсказки на одной странице - пример SiteWidget']['id'] ?? null;
$multi = $demoOnboardings['Переход между страницами - пример SiteWidget']['id'] ?? null;
if ($one === null || $multi === null) {
    return;
}
$launch = static fn(int $id): array => ['/onboarding', 'sitewidget_onboarding' => $id, 'sitewidget_step' => 0];
$this->registerCssFile('/css/onboarding-demo.css', ['depends' => [\app\Presentation\Yii\Asset\AppAsset::class]]);
?>
<section id="live-demo" class="site-landing__section sw-demo">
    <div class="site-landing__inner">
        <p class="sw-demo__eyebrow">Попробуйте на этом сайте</p>
        <h2 id="<?= $arrival ? 'sw-demo-arrival' : 'sw-demo-title' ?>"><?= $arrival ? 'Из подсказки - в подробную инструкцию' : 'Не читайте про онбординг. Пройдите его.' ?></h2>
        <p class="site-landing__section-lead"><?= $arrival ? 'Здесь продолжается маршрут со страницы онбординга. А значок вопроса у примера ниже открывает подсказку со статьёй внутри виджета.' : 'Выберите короткий тур или маршрут с переходом на другую страницу. Оба запускаются только по вашему нажатию.' ?></p>
        <div class="sw-demo__examples">
            <article id="sw-demo-hint-target">
                <img src="/img/sitewidget-module-onboarding.svg" alt="" width="32" height="32">
                <h3>На одной странице</h3>
                <p>Четыре подсказки покажут, как объяснить элементы страницы и связать их в маршрут.</p>
                <?= Html::a('Попробовать онбординг', $launch((int)$one), ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </article>
            <article id="sw-demo-route">
                <img src="/img/sitewidget-module-navigator.svg" alt="" width="32" height="32">
                <h3>С переходом на другую страницу</h3>
                <p>Начните на странице онбординга. Кнопка «Далее» откроет инструкции, и обучение продолжится.</p>
                <?= Html::a('Пройти маршрут', $launch((int)$multi), ['class' => 'site-landing__button site-landing__button--ghost']) ?>
            </article>
            <article>
                <img src="/img/sitewidget-module-instructions.svg" alt="" width="32" height="32">
                <h3>Подсказка со статьёй</h3>
                <p id="sw-demo-article">Нажмите значок вопроса рядом с этим текстом. Из подсказки откроется статья - без перехода на другую страницу.</p>
            </article>
        </div>
        <?php if ($arrival): ?>
            <p class="sw-demo__back"><?= Html::a('Вернуться к странице онбординга', ['/onboarding', '#' => 'live-demo']) ?></p>
        <?php endif; ?>
    </div>
</section>
