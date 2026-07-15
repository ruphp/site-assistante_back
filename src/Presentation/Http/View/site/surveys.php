<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Опросы и анкеты для сайта: модуль сбора заявок | SiteWidget';
$this->params['seoDescription'] = 'Модуль опросов и анкет для сайта в SiteWidget. Собирайте заявки, уточняйте потребности посетителей и задавайте вопросы без отдельной формы на странице.';
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
    'serviceType' => 'Модуль опросов и анкет для сайта',
    'url' => 'https://sitewidget.ru/surveys',
    'description' => $this->params['seoDescription'],
];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $this->params['seoDescription'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'модуль опросов, опрос для сайта, онлайн опросы для сайта, анкета на сайт создать, создать опрос на сайте, анкеты на сайт',
]);

$cards = [
    [
        'title' => 'Опросы для сайта',
        'text' => 'Задавайте посетителю несколько коротких вопросов прямо в виджете, не уводя его на отдельную страницу.',
    ],
    [
        'title' => 'Анкеты и заявки',
        'text' => 'Собирайте контакты, параметры заказа, пожелания, ответы по товару или услуге в понятном пошаговом формате.',
    ],
    [
        'title' => 'Продолжение диалога',
        'text' => 'Если по ответам нужен менеджер, посетитель может сразу перейти в чат поддержки с уже понятным контекстом.',
    ],
];

$steps = [
    'Создайте анкету: название, приветствие и набор вопросов.',
    'Выберите типы вопросов: текст, варианты ответа, контакты или уточняющие поля.',
    'Покажите анкету модальным окном на нужной странице или после действия посетителя.',
    'Получайте ответы в панели и используйте их для обработки заявки или консультации.',
];
?>

<main class="site-landing site-docs">
    <section class="site-landing__section site-docs__hero">
        <div class="site-landing__inner">
            <div class="site-landing__eyebrow">Опросы, анкеты и заявки</div>
            <h1>Опросы и анкеты для сайта</h1>
            <p class="site-landing__lead">
                SiteWidget помогает создать анкету или опрос для сайта: задать вопросы,
                собрать контакты, уточнить потребности посетителя и передать ответы менеджеру.
                Это удобнее, чем отдельная форма, которую сложно найти и легко бросить на середине.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Начать использовать', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <a class="site-landing__button site-landing__button--ghost" href="#how">Как работает</a>
            </div>
        </div>
    </section>

    <section id="use-cases" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Модуль опросов рядом с виджетом</h2>
            <p class="site-landing__section-lead">
                Анкета открывается отдельным модальным окном, а в виджете можно показать непройденные
                анкеты. Посетитель отвечает на вопросы по шагам, а владелец сайта получает более понятную заявку.
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
            <h2>Как создать опрос на сайте</h2>
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
                    и не перегружает посетителя большим списком полей.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Подбор товара, услуги, тарифа или комплектации.</li>
                <li>Сбор заявки с уточняющими вопросами.</li>
                <li>Мини-опрос после обращения или покупки.</li>
                <li>Сегментация посетителей перед консультацией менеджера.</li>
                <li>Сбор данных без отдельной страницы формы.</li>
            </ul>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner">
            <div class="site-landing__cta">
                <h2>Анкеты работают вместе с поддержкой и инструкциями</h2>
                <p class="site-landing__section-lead">
                    Посетитель может сначала пройти опрос, потом открыть инструкцию или написать менеджеру.
                    Так SiteWidget помогает не только принять обращение, но и собрать полезный контекст.
                </p>
                <?= Html::a('Подключить SiteWidget', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </div>
        </div>
    </section>
</main>
