<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Онлайн чат для сайта и онлайн консультант | SiteWidget';
$this->params['seoDescription'] = 'Онлайн чат для сайта и онлайн консультант: общение с посетителями, push-уведомления и ответы из приложения Android или Telegram.';
$this->params['seoCanonical'] = '/';
$this->params['seoSchemas'][] = [
    '@context' => 'https://schema.org',
    '@type' => 'WebApplication',
    'name' => 'SiteWidget',
    'alternateName' => 'Онлайн чат для сайта',
    'applicationCategory' => 'BusinessApplication',
    'browserRequirements' => 'JavaScript',
    'url' => 'https://sitewidget.ru/',
    'description' => $this->params['seoDescription'],
    'featureList' => [
        'форма обратной связи на сайт',
        'онлайн чат и консультант для сайта',
        'быстрые кнопки обращений',
        'уведомления в Android-приложении',
        'уведомления и ответы через Telegram-бота',
        'FAQ и инструкции для пользователей сайта',
        'онбординг и подсказки на сайте',
        'опросы и анкеты для сайта',
        'подтверждение телефона при регистрации через SMS',
    ],
    'offers' => [
        '@type' => 'AggregateOffer',
        'lowPrice' => '0',
        'highPrice' => '999',
        'priceCurrency' => 'RUB',
        'availability' => 'https://schema.org/InStock',
    ],
];
$this->registerMetaTag([
        'name' => 'description',
        'content' => $this->params['seoDescription'],
]);
$this->registerMetaTag([
        'name' => 'keywords',
        'content' => 'онлайн чат для сайта, чат для сайта, онлайн консультант для сайта, виджет поддержки, FAQ для сайта, SiteWidget',
]);
?>

<main class="site-landing">
    <section class="site-landing__hero">
        <div>
            <div class="site-landing__eyebrow">Вопросы посетителей не теряются</div>
            <h1>Онлайн чат для сайта - отвечайте из приложения Android или Telegram</h1>
            <p class="site-landing__lead">
                Посетитель задаёт вопрос прямо на сайте, менеджер получает уведомление и отвечает.
                Диалог и история остаются в панели SiteWidget. FAQ, инструкции, онбординг и анкеты дополняют онлайн-поддержку.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Начать бесплатно', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <button class="site-landing__button site-landing__button--ghost" type="button" data-sitewidget-open-support>Открыть живой чат</button>
            </div>
            <div class="site-landing__hero-facts" aria-label="Ключевые возможности">
                <span>Чат на вашем сайте</span><span>Ответы из приложения Android и Telegram</span><span>История диалогов</span>
            </div>
        </div>

        <div class="site-landing__mockup" aria-label="Пример виджета на сайте">
            <div class="site-landing__browser">
                <div class="site-landing__browser-top">
                    <span class="site-landing__dot"></span>
                    <span class="site-landing__dot"></span>
                    <span class="site-landing__dot"></span>
                </div>
                <div class="site-landing__browser-body">
                    <div class="site-landing__page-copy"><strong>Ваш сайт</strong><span></span><span></span><span></span></div>
                    <div class="site-landing__manager-alert"><small>Новое обращение</small><strong>«Этот товар есть в наличии?»</strong><span>Ответить из Android или Telegram</span></div>
                </div>
            </div>
            <div class="site-landing__widget">
                <div class="site-landing__widget-head">Онлайн чат</div>
                <div class="site-landing__widget-body">
                    <div class="site-landing__message site-landing__message--visitor">Здравствуйте! Этот товар есть в наличии?</div>
                    <div class="site-landing__message site-landing__message--operator">Здравствуйте! Да, есть. Подскажите, нужна доставка или самовывоз?</div>
                </div>
            </div>
        </div>
    </section>

    <section id="modules" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Онлайн чат - основа виджета</h2>
            <p class="site-landing__section-lead">
                При открытии посетитель сразу видит онлайн-связь. FAQ, инструкции, онбординг и анкеты остаются дополнительными модулями.
            </p>
            <div class="site-landing__grid">
                <article class="site-landing__card" id="support">
                    <img class="site-landing__card-icon" src="/img/sitewidget-module-support.svg" alt="">
                    <h3>Онлайн консультант для сайта</h3>
                    <p>Чат с оператором, форма обратной связи, быстрые кнопки тем и тикеты. Менеджеры получают уведомления в приложении Android и Telegram-боте.</p>
                </article>
                <article class="site-landing__card">
                    <img class="site-landing__card-icon" src="/img/sitewidget-module-instructions.svg" alt="">
                    <h3>FAQ и инструкции</h3>
                    <p>FAQ, справочные статьи и инструкции внутри виджета. Подсказка на странице может открыть нужную инструкцию в один клик.</p>
                    <?= Html::a('Подробнее об инструкциях', ['/faq-instructions'], ['class' => 'site-landing__link']) ?>
                </article>
                <article class="site-landing__card">
                    <img class="site-landing__card-icon" src="/img/sitewidget-module-onboarding.svg" alt="">
                    <h3>Онбординг на сайт</h3>
                    <p>Пошаговые подсказки к элементам страницы: провести посетителя по сценарию, открыть инструкцию или довести до действия.</p>
                    <?= Html::a('Подробнее об онбординге', ['/onboarding'], ['class' => 'site-landing__link']) ?>
                </article>
                <article class="site-landing__card">
                    <img class="site-landing__card-icon" src="/img/sitewidget-checkbox.svg" alt="">
                    <h3>Опросы и анкеты</h3>
                    <p>Пошаговые анкеты и мини-опросы: вопросы, контакты, вводные по задаче, обратная связь и возможность вернуться к заполнению позже.</p>
                    <?= Html::a('Подробнее об анкетах', ['/surveys'], ['class' => 'site-landing__link']) ?>
                </article>
            </div>
        </div>
    </section>

    <section id="how" class="site-landing__section">
        <div class="site-landing__inner">
            <h2>От вопроса до ответа - три шага</h2>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>01. Посетитель спрашивает</strong>
                    <p>Онлайн чат открывается первым. Посетитель пишет вопрос или выбирает быструю тему.</p>
                </div>
                <div class="site-landing__step">
                    <strong>02. Менеджер получает сигнал</strong>
                    <p>Менеджер получает уведомление в приложении Android или Telegram-боте, чтобы вопрос не затерялся.</p>
                </div>
                <div class="site-landing__step">
                    <strong>03. Ответ остаётся в диалоге</strong>
                    <p>Менеджер отвечает из удобного канала, посетитель видит ответ в виджете, а история сохраняется.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="android-app" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <div class="site-landing__app-head">
                <div>
                    <h2>Приложение для менеджера</h2>
                    <p class="site-landing__section-lead">
                        Android-приложение помогает не пропускать обращения: отправляет менеджеру push-уведомления о новых обращениях и неотвеченных диалогах, даже в свернутом режиме.
                    </p>
                </div>
                <div class="site-landing__actions">
                    <?= Html::a('Скачать APK', ['/sitewidgetmanager.apk'], [
                        'class' => 'site-landing__button site-landing__button--primary',
                        'download' => true,
                    ]) ?>
                </div>
            </div>
        </div>
    </section>

    <section id="telegram-bot" class="site-landing__section">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Telegram-бот для менеджеров</h2>
                <p class="site-landing__section-lead">
                    Если Android-приложение не подходит, менеджер может получать обращения и отвечать посетителям прямо из Telegram.
                    Бот показывает открытые диалоги, даёт быстро ответить и помогает не пропустить горячий вопрос.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Уведомления о новых обращениях.</li>
                <li>Ответ посетителю без входа в панель управления.</li>
                <li>Закрытие диалога владельцем аккаунта.</li>
            </ul>
            <div class="site-landing__actions">
                <?= Html::a('Открыть бота', 'https://t.me/SiteWidgetBot', [
                    'class' => 'site-landing__button site-landing__button--primary',
                    'target' => '_blank',
                    'rel' => 'noopener',
                ]) ?>
            </div>
        </div>
    </section>

    <section id="integrations" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Готовые модули для CMS и интернет-магазинов</h2>
                <p class="site-landing__section-lead">
                    Для WordPress, Joomla и OpenCart можно подключить SiteWidget без ручной интеграции: модуль сам
                    передаст нужные данные сайта, пользователя и ролей.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Быстрое подключение SiteWidget к WordPress, Joomla и OpenCart.</li>
                <li>Интеграция авторизованного посетителя с виджетом без лишней настройки кода.</li>
                <li>Настройка доступа к контенту по ролям пользователя сайта <span>(только в платных тарифах)</span>.</li>
            </ul>
            <div class="site-landing__download">
                <a href="/cms-plugins#wordpress">
                    <strong>WordPress</strong>
                    <span>Скачать плагин, установить и указать public key</span>
                </a>
                <a href="/cms-plugins#joomla">
                    <strong>Joomla</strong>
                    <span>Скачать system plugin и включить его в админке</span>
                </a>
                <a href="/cms-plugins#opencart">
                    <strong>OpenCart</strong>
                    <span>Установить модуль .ocmod.zip и настроить витрину</span>
                </a>
                <a href="/cms-plugins">
                    <strong>GitHub</strong>
                    <span>Все ссылки, версии и короткая инструкция по установке</span>
                </a>
            </div>
        </div>
    </section>

    <section id="sms-verification" class="site-landing__section">
        <div class="site-landing__inner">
            <h2>Подтверждение телефона при регистрации через SMS</h2>
            <p class="site-landing__section-lead">
                SiteWidget помогает небольшому проекту подтвердить номер нового пользователя без отдельного SMS-шлюза.
                Сообщение отправляется владельцем сайта из Android-приложения с использованием SMS его мобильного тарифа.
            </p>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>Сайт создаёт код</strong>
                    <p>Backend или CMS-модуль сохраняет хеш кода и передаёт в SiteWidget номер и готовый текст сообщения.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Владелец отправляет SMS</strong>
                    <p>Android-приложение показывает запрос и открывает стандартное SMS-приложение с заполненными данными.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Телефон подтверждён один раз</strong>
                    <p>После проверки кода регистрация продолжается. При следующих входах повторное подтверждение не требуется.</p>
                </div>
            </div>
            <p class="site-landing__section-lead">Функция входит в тариф Start. Для отправки используются SMS, доступные на мобильном тарифе владельца сайта.</p>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner">
            <h2>Обратная связь с сайта без пропущенных заявок</h2>
            <p class="site-landing__section-lead">
                Обращения не теряются в почте и не ждут, пока кто-то случайно откроет админку.
                Менеджер получает уведомления в Android-приложении и Telegram-боте, видит новые и
                неотвеченные диалоги, а владелец сайта контролирует историю общения.
            </p>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>Горячие вопросы</strong>
                    <p>Новые обращения и неотвеченные диалоги подсвечиваются, чтобы менеджер быстро понял, где нужен ответ.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Android и Telegram</strong>
                    <p>Push-уведомления, активные напоминания и ответы из приложения или Telegram помогают не пропускать посетителей.</p>
                </div>
                <div class="site-landing__step">
                    <strong>История диалогов</strong>
                    <p>Все обращения остаются в панели управления: видно, кто писал, что спросил и как менеджер ответил.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Выберите тариф под вашу задачу</h2>
            <p class="site-landing__section-lead">
                Начните бесплатно и подключите расширенные возможности, когда они понадобятся.
            </p>

            <div class="site-landing__pricing">
                <article class="site-landing__price-card">
                    <div class="site-landing__price-head">
                        <span>Free</span>
                        <strong>0 ₽</strong>
                    </div>
                    <p>Для первого подключения и проверки виджета. При первом запуске можно попробовать весь функционал.</p>
                    <ul>
                        <li>50 ответов операторов в день</li>
                        <li>300 диалогов и 3000 сообщений в месяц</li>
                        <li>1 проект</li>
                        <li>1 оператор</li>
                        <li>Онлайн чат, диалоги и история обращений</li>
                        <li>История 30 дней</li>
                    </ul>
                    <div class="site-landing__price-action">
                        <?= Html::a('Начать бесплатно', ['/join'], ['class' => 'site-landing__button site-landing__button--ghost']) ?>
                    </div>
                </article>

                <article class="site-landing__price-card site-landing__price-card--accent">
                    <div class="site-landing__price-head">
                        <span>Start</span>
                        <strong>499 ₽/мес</strong>
                    </div>
                    <p>Для сайта, где обращения уже идут регулярно.</p>
                    <ul>
                        <li>Все возможности Free</li>
                        <li>500 ответов операторов в день</li>
                        <li>3000 диалогов и 30000 сообщений в месяц</li>
                        <li>Модуль инструкций: разделы, статьи, избранное и отзывы пользователей</li>
                        <li>Модуль онбординга: подсказки и сценарии навигации по сайту</li>
                        <li>Анкетирование: формы, вопросы и сбор данных</li>
                        <li>Роли пользователей сайта для фильтрации контента</li>
                        <li>До 3 операторов</li>
                        <li>До 5 кнопок - быстрые обращения</li>
                        <li>История 90 дней</li>
                    </ul>
                    <div class="site-landing__price-action">
                        <?= Html::a('Подключить Start', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                    </div>
                </article>

                <article class="site-landing__price-card">
                    <div class="site-landing__price-head">
                        <span>Pro</span>
                        <strong>999 ₽/мес</strong>
                    </div>
                    <p>Для нескольких сайтов, большей команды и повышенного объема обращений.</p>
                    <ul>
                        <li>Все возможности Start</li>
                        <li>1500 ответов операторов в день</li>
                        <li>10000 диалогов и 100000 сообщений в месяц</li>
                        <li>До 3 проектов</li>
                        <li>До 5 операторов</li>
                    </ul>
                    <div class="site-landing__price-action">
                        <button class="site-landing__button site-landing__button--ghost" type="button" data-sitewidget-open-support>Обсудить Pro</button>
                    </div>
                </article>
            </div>

            <div class="site-landing__business">
                <strong>Для высоконагруженных сайтов</strong>
                <span>
                    Можно согласовать индивидуальный лимит, отдельные условия хранения истории и расширенные настройки
                    нагрузки.
                </span><br><br>
                <strong>Сейчас Start и Pro подключаются через поддержку.</strong>
                <div class="site-landing__actions">
                    <button class="site-landing__button site-landing__button--primary" type="button" data-sitewidget-open-support>
                        Написать в поддержку
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner">
            <div class="site-landing__cta">
                <h2>Проверьте онлайн-поддержку на своём сайте</h2>
                <p class="site-landing__section-lead">
                    Создайте проект, установите код или CMS-модуль и проведите первый тестовый диалог.
                </p>
                <?= Html::a('Создать проект', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </div>
        </div>
    </section>
</main>
