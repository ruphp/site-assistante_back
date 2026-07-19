<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Модуль вопрос-ответ и инструкции для сайта | SiteWidget';
$this->params['seoDescription'] = 'Добавьте на сайт FAQ, инструкции для пользователей и справочные статьи внутри виджета SiteWidget. Посетители находят ответы сами, а менеджеры получают меньше однотипных обращений.';
$this->params['seoCanonical'] = '/faq-instructions';
$this->params['seoBreadcrumbs'] = [
    ['name' => 'Главная', 'url' => '/'],
    ['name' => 'FAQ и инструкции', 'url' => '/faq-instructions'],
];
$this->params['seoSchemas'][] = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => 'FAQ и инструкции для сайта',
    'provider' => [
        '@type' => 'Organization',
        'name' => 'SiteWidget',
        'url' => 'https://sitewidget.ru',
    ],
    'serviceType' => 'Модуль вопрос-ответ и инструкции для сайта',
    'url' => 'https://sitewidget.ru/faq-instructions',
    'description' => $this->params['seoDescription'],
];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $this->params['seoDescription'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'модуль вопрос ответ, FAQ на сайт, инструкции для пользователей сайта, руководство пользователя для сайта, база знаний на сайт, справка на сайте',
]);

$cases = [
    [
        'title' => 'Частые вопросы',
        'text' => 'Соберите типовые вопросы по оплате, доставке, регистрации, возвратам или работе сервиса в понятный список.',
    ],
    [
        'title' => 'Инструкции для пользователей',
        'text' => 'Покажите, где что находится на сайте, как оформить заказ, где скачать документ или как настроить личный кабинет.',
    ],
    [
        'title' => 'Контекстная помощь',
        'text' => 'Подсказка рядом с кнопкой, формой или блоком может открыть нужную инструкцию именно в момент вопроса.',
    ],
];

$steps = [
    'Создайте разделы и подразделы: например, оплата, доставка, личный кабинет, документы.',
    'Добавьте инструкции с текстом, ссылками, изображениями или видео.',
    'Свяжите инструкцию с подсказкой, кнопкой или нужным местом на странице.',
    'Посетитель открывает FAQ и инструкции внутри виджета, не уходя со страницы.',
];
?>

<main class="site-landing site-docs">
    <section class="site-landing__section site-docs__hero">
        <div class="site-landing__inner">
            <div class="site-landing__eyebrow">FAQ, справка и инструкции</div>
            <h1>Модуль вопрос-ответ и инструкции для сайта</h1>
            <p class="site-landing__lead">
                SiteWidget добавляет на сайт раздел помощи внутри виджета: FAQ, инструкции для пользователей,
                справочные статьи и короткие ответы на частые вопросы. Посетитель находит нужную информацию
                без поиска по сайту и без лишнего обращения к менеджеру. А если короткой подсказки мало,
                она может открыть нужную инструкцию прямо в месте, где возник вопрос.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Начать использовать', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <a class="site-landing__button site-landing__button--ghost" href="#how">Как работает</a>
            </div>
        </div>
    </section>

    <section class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>FAQ на сайте, который не уводит посетителя со страницы</h2>
            <p class="site-landing__section-lead">
                Обычный раздел помощи часто спрятан где-то в меню. В SiteWidget вопрос-ответ и инструкции
                открываются прямо рядом с текущей страницей: человек читает ответ и продолжает действие.
            </p>
            <div class="site-landing__grid">
                <?php foreach ($cases as $case): ?>
                    <article class="site-landing__card">
                        <img class="site-landing__card-icon" src="/img/sitewidget-module-instructions.svg" alt="">
                        <h3><?= Html::encode($case['title']) ?></h3>
                        <p><?= Html::encode($case['text']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="how" class="site-landing__section">
        <div class="site-landing__inner">
            <h2>Как работает модуль инструкций</h2>
            <div class="site-landing__steps">
                <?php foreach ($steps as $index => $step): ?>
                    <div class="site-landing__step">
                        <strong><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?>. <?= Html::encode($index === 0 ? 'Структура' : ($index === 1 ? 'Контент' : ($index === 2 ? 'Показ' : 'Ответ'))) ?></strong>
                        <p><?= Html::encode($step) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Инструкция открывается там, где возник вопрос</h2>
                <p class="site-landing__section-lead">
                    Подсказку можно привязать к конкретной кнопке, форме, пункту меню или блоку сайта.
                    Если короткого пояснения недостаточно, посетитель нажимает «Подробнее» и открывает
                    выбранную инструкцию прямо в виджете.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Подсказка объясняет элемент страницы коротко и по делу.</li>
                <li>Кнопка в подсказке открывает выбранную инструкцию.</li>
                <li>Посетитель остается на текущей странице и не теряет контекст.</li>
                <li>Если ответа не хватило, он сразу пишет в онлайн-поддержку.</li>
            </ul>
        </div>
    </section>

    <section id="use-cases" class="site-landing__section">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Когда это полезно</h2>
                <p class="site-landing__section-lead">
                    Модуль подходит интернет-магазинам, сервисам, личным кабинетам, образовательным проектам
                    и любым сайтам, где посетители часто задают одни и те же вопросы.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Снизить количество однотипных обращений в поддержку.</li>
                <li>Объяснить сложные действия на сайте без отдельной страницы документации.</li>
                <li>Дать менеджеру ссылку на готовую инструкцию вместо длинного ответа вручную.</li>
                <li>Показать посетителю справку именно там, где он застрял.</li>
            </ul>
        </div>
    </section>

    <section class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <div class="site-landing__cta">
                <h2>FAQ и инструкции работают вместе с обратной связью</h2>
                <p class="site-landing__section-lead">
                    Если инструкции хватило — менеджер не отвлекается. Если вопрос остался — посетитель сразу
                    пишет в чат обратной связи, а оператор видит контекст обращения.
                </p>
                <?= Html::a('Подключить SiteWidget', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </div>
        </div>
    </section>
</main>
