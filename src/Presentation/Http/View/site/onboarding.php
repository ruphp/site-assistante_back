<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Онбординг пользователей на сайте: подсказки и интерактивные инструкции | SiteWidget';
$this->params['seoDescription'] = 'Онбординг пользователей на сайте с помощью интерактивных подсказок SiteWidget. Покажите посетителю важные элементы, объясните сценарий и доведите до нужного действия.';
$this->params['seoCanonical'] = '/onboarding';
$this->params['seoBreadcrumbs'] = [
    ['name' => 'Главная', 'url' => '/'],
    ['name' => 'Онбординг', 'url' => '/onboarding'],
];
$this->params['seoSchemas'][] = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => 'Онбординг пользователей на сайте',
    'provider' => [
        '@type' => 'Organization',
        'name' => 'SiteWidget',
        'url' => 'https://sitewidget.ru',
    ],
    'serviceType' => 'Онбординг, подсказки и интерактивные инструкции для сайта',
    'url' => 'https://sitewidget.ru/onboarding',
    'description' => $this->params['seoDescription'],
];
$this->registerMetaTag([
    'name' => 'description',
    'content' => $this->params['seoDescription'],
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'онбординг, онбординг пользователей, подсказки на сайте, интерактивная инструкция, интерактивная подсказка, путеводитель по сайту, навигатор по сайту',
]);

$cards = [
    [
        'title' => 'Подсказки на сайте',
        'text' => 'Короткие пояснения появляются рядом с нужным элементом страницы и могут открыть подробную инструкцию.',
    ],
    [
        'title' => 'Интерактивная инструкция',
        'text' => 'Несколько подсказок объединяются в понятный маршрут: посетитель идет по шагам и видит, что делать дальше.',
    ],
    [
        'title' => 'Путеводитель по сайту',
        'text' => 'Онбординг помогает объяснить сложный интерфейс, личный кабинет, каталог, оформление заказа или новый раздел.',
    ],
];

$steps = [
    'Вы выбираете страницу и элемент, к которому должна быть привязана подсказка.',
    'Добавляете короткий текст, кнопку, ссылку или переход к нужной инструкции внутри виджета.',
    'Собираете подсказки в сценарий онбординга и задаете порядок шагов.',
    'Посетитель проходит сценарий на сайте и быстрее понимает, куда нажимать.',
];
?>

<main class="site-landing site-docs">
    <section class="site-landing__section site-docs__hero">
        <div class="site-landing__inner">
            <div class="site-landing__eyebrow">Онбординг, подсказки и навигация</div>
            <h1>Онбординг пользователей на сайте</h1>
            <p class="site-landing__lead">
                SiteWidget помогает объяснить посетителю сайт простыми шагами: показывает подсказки рядом
                с элементами страницы, ведет по сценарию и превращает сложный интерфейс в понятный маршрут.
                Подсказка может быть самостоятельной, частью онбординга или открывать подробную инструкцию
                внутри виджета. Это не кадровый онбординг сотрудников, а продуктовый онбординг для посетителей сайта.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Начать использовать', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <a class="site-landing__button site-landing__button--ghost" href="#use-cases">Где применять</a>
            </div>
        </div>
    </section>

    <section class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Что такое онбординг простыми словами</h2>
            <p class="site-landing__section-lead">
                Онбординг на сайте — это серия подсказок, которая знакомит пользователя с интерфейсом:
                где находится нужная кнопка, как заполнить форму, как пройти регистрацию, оформить заказ
                или найти важный раздел.
            </p>
            <div class="site-landing__grid">
                <?php foreach ($cards as $card): ?>
                    <article class="site-landing__card">
                        <img class="site-landing__card-icon" src="/img/sitewidget-module-onboarding.svg" alt="">
                        <h3><?= Html::encode($card['title']) ?></h3>
                        <p><?= Html::encode($card['text']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner">
            <h2>Как работает интерактивная инструкция</h2>
            <div class="site-landing__steps">
                <?php foreach ($steps as $index => $step): ?>
                    <div class="site-landing__step">
                        <strong><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?>. <?= Html::encode($index === 0 ? 'Элемент' : ($index === 1 ? 'Подсказка' : ($index === 2 ? 'Сценарий' : 'Результат'))) ?></strong>
                        <p><?= Html::encode($step) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="use-cases" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Где онбординг особенно полезен</h2>
                <p class="site-landing__section-lead">
                    Если посетитель не понимает, где начать, он часто закрывает страницу или пишет менеджеру.
                    Онбординг снимает часть вопросов до обращения в поддержку.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Первый вход в личный кабинет или сервис.</li>
                <li>Регистрация, авторизация и заполнение профиля.</li>
                <li>Оформление заказа, заявки или анкеты.</li>
                <li>Объяснение нового раздела, фильтра, тарифа или сложной формы.</li>
                <li>Подсказки для пользователей разных ролей на сайте.</li>
            </ul>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Подсказки, инструкции и онбординг работают вместе</h2>
                <p class="site-landing__section-lead">
                    Один и тот же элемент страницы можно объяснить коротко или включить в пошаговый сценарий.
                    Если в подсказку не помещается весь ответ, кнопка открывает нужную инструкцию в виджете.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Самостоятельная подсказка отвечает на короткий вопрос рядом с элементом.</li>
                <li>Шаг онбординга ведет посетителя по заранее собранному маршруту.</li>
                <li>Кнопка «Подробнее» открывает инструкцию, не уводя человека со страницы.</li>
                <li>После инструкции посетитель может продолжить сценарий или написать менеджеру.</li>
            </ul>
        </div>
    </section>

    <section class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <div class="site-landing__cta">
                <h2>Онбординг работает вместе с обратной связью</h2>
                <p class="site-landing__section-lead">
                    Сначала виджет помогает посетителю разобраться самому. Если вопрос остался, он может
                    сразу написать в онлайн-поддержку, а менеджер увидит обращение в панели, Android-приложении
                    или Telegram-боте.
                </p>
                <?= Html::a('Подключить SiteWidget', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </div>
        </div>
    </section>
</main>
