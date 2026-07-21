<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Опросы и анкеты для сайта: пошаговые формы в виджете | SiteWidget';
$this->params['seoDescription'] = 'Модуль опросов и анкет для сайта в SiteWidget. Создавайте пошаговые анкеты, собирайте ответы посетителей, контакты и вводные без отдельной страницы формы.';
$this->params['seoCanonical'] = '/surveys';
$this->params['seoBreadcrumbs'] = [
    ['name' => 'Главная', 'url' => '/'],
    ['name' => 'Опросы и анкеты', 'url' => '/surveys'],
];
$this->params['seoSchemas'][] = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => 'Опросы и анкеты для сайта',
    'provider' => [
        '@type' => 'Organization',
        'name' => 'SiteWidget',
        'url' => 'https://sitewidget.ru',
    ],
    'serviceType' => 'Модуль опросов, анкет и пошаговых форм для сайта',
    'url' => 'https://sitewidget.ru/surveys',
    'description' => $this->params['seoDescription'],
];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $this->params['seoDescription'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'модуль опросов, опрос для сайта, онлайн опросы для сайта, анкета на сайт создать, создать опрос на сайте, анкеты на сайт, пошаговая форма на сайт',
]);

$cards = [
    [
        'title' => 'Мини-опросы на сайте',
        'text' => 'Задавайте посетителю несколько коротких вопросов прямо на странице, не отправляя его в отдельный раздел.',
    ],
    [
        'title' => 'Пошаговые анкеты',
        'text' => 'Собирайте контакты, вводные по задаче, предпочтения, обратную связь и ответы по товару или услуге.',
    ],
    [
        'title' => 'Отложенное заполнение',
        'text' => 'Если посетитель не готов ответить сразу, анкета остаётся в виджете, и к ней можно вернуться позже.',
    ],
];

$steps = [
    'Создайте анкету: название, порядок показа и набор вопросов.',
    'Выберите типы ответов: текст, один вариант, несколько вариантов или оценка.',
    'Покажите анкету модальным окном на нужной странице или оставьте доступной в виджете.',
    'Получайте ответы в панели и используйте их для консультации, сегментации или улучшения страницы.',
];
?>

<main class="site-landing site-docs">
    <section class="site-landing__section site-docs__hero">
        <div class="site-landing__inner">
            <div class="site-landing__eyebrow">Опросы, анкеты и пошаговые формы</div>
            <h1>Опросы и анкеты для сайта</h1>
            <p class="site-landing__lead">
                SiteWidget помогает создать анкету или опрос для сайта: задать вопросы,
                собрать контакты, уточнить вводные посетителя, получить обратную связь и сохранить ответы
                в панели управления. Это удобнее, чем отдельная форма, которую сложно найти и легко бросить
                на середине.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Начать использовать', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <a class="site-landing__button site-landing__button--ghost" href="#how">Как работает</a>
            </div>
        </div>
    </section>

    <section id="use-cases" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Модуль анкет прямо рядом с виджетом</h2>
            <p class="site-landing__section-lead">
                Анкета открывается отдельным модальным окном, а непройденные анкеты остаются в виджете.
                Посетитель отвечает на вопросы по шагам, а владелец сайта получает структурированные ответы
                без длинной формы на странице.
            </p>
            <div class="site-landing__grid">
                <?php foreach ($cards as $card): ?>
                    <article class="site-landing__card">
                        <img class="site-landing__card-icon" src="/img/sitewidget-checkbox.svg" alt="">
                        <h3><?= Html::encode($card['title']) ?></h3>
                        <p><?= Html::encode($card['text']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="how" class="site-landing__section">
        <div class="site-landing__inner">
            <h2>Как создать анкету на сайте</h2>
            <div class="site-landing__steps">
                <?php foreach ($steps as $index => $step): ?>
                    <div class="site-landing__step">
                        <strong><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?>. <?= Html::encode($index === 0 ? 'Анкета' : ($index === 1 ? 'Вопросы' : ($index === 2 ? 'Показ' : 'Ответы'))) ?></strong>
                        <p><?= Html::encode($step) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Когда анкета лучше обычной формы</h2>
                <p class="site-landing__section-lead">
                    Обычная форма часто просит всё сразу. Пошаговая анкета помогает мягко уточнить данные
                    и не перегружает посетителя большим списком полей. Такой формат подходит не только для заявок,
                    но и для обратной связи, подбора, оценки и коротких исследований на сайте.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Подбор товара, услуги, тарифа или комплектации.</li>
                <li>Сбор вводных перед консультацией менеджера.</li>
                <li>Мини-опрос после обращения, покупки или просмотра важной страницы.</li>
                <li>Сегментация посетителей перед консультацией менеджера.</li>
                <li>Оценка удобства страницы, товара, услуги или инструкции.</li>
                <li>Сбор данных без отдельной страницы формы.</li>
            </ul>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner">
            <div class="site-landing__cta">
                <h2>Анкеты работают вместе с поддержкой и инструкциями</h2>
                <p class="site-landing__section-lead">
                    Посетитель может пройти анкету, открыть инструкцию или написать менеджеру.
                    Так SiteWidget помогает не только принять обращение, но и собрать полезный контекст
                    до начала диалога.
                </p>
                <?= Html::a('Подключить SiteWidget', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </div>
        </div>
    </section>
</main>
