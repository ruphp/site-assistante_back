<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Обратная связь на сайт: форма, чат и виджет поддержки | SiteWidget';
$this->params['seoDescription'] = 'SiteWidget добавляет на сайт форму обратной связи, чат с менеджером, быстрые кнопки обращений, Android-уведомления, Telegram-бота, базу знаний, анкеты и онбординг.';
$this->params['seoCanonical'] = '/';
$this->params['seoSchemas'][] = [
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => 'SiteWidget',
    'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Web, Android, Telegram',
    'url' => 'https://sitewidget.ru/',
    'description' => $this->params['seoDescription'],
    'offers' => [
        '@type' => 'AggregateOffer',
        'lowPrice' => '0',
        'highPrice' => '999',
        'priceCurrency' => 'RUB',
    ],
];
$this->params['seoSchemas'][] = [
    '@context' => 'https://schema.org',
    '@type' => 'WebApplication',
    'name' => 'SiteWidget',
    'alternateName' => 'Виджет обратной связи для сайта',
    'applicationCategory' => 'BusinessApplication',
    'browserRequirements' => 'JavaScript',
    'url' => 'https://sitewidget.ru/',
    'description' => $this->params['seoDescription'],
    'featureList' => [
        'форма обратной связи на сайт',
        'онлайн-поддержка и чат с менеджером',
        'быстрые кнопки обращений',
        'уведомления в Android-приложении',
        'уведомления и ответы через Telegram-бота',
        'FAQ и инструкции для пользователей сайта',
        'онбординг и подсказки на сайте',
        'опросы и анкеты для сайта',
    ],
    'offers' => [
        '@type' => 'AggregateOffer',
        'lowPrice' => '0',
        'highPrice' => '999',
        'priceCurrency' => 'RUB',
        'availability' => 'https://schema.org/InStock',
    ],
];
$this->params['seoSchemas'][] = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => 'Виджет обратной связи и онлайн-поддержки для сайта',
    'provider' => [
        '@type' => 'Organization',
        'name' => 'SiteWidget',
        'url' => 'https://sitewidget.ru',
    ],
    'serviceType' => 'Форма обратной связи, чат поддержки, FAQ, онбординг, опросы и анкеты для сайта',
    'url' => 'https://sitewidget.ru/',
    'description' => $this->params['seoDescription'],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => 'Модули SiteWidget',
        'itemListElement' => [
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => 'Онлайн-поддержка на сайт',
                    'description' => 'Чат с менеджером, обращения как тикеты, быстрые кнопки тем, Android-уведомления и Telegram-бот.',
                ],
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => 'FAQ и инструкции для сайта',
                    'description' => 'Разделы, справочные статьи, полезные ответы и инструкции внутри виджета.',
                ],
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => 'Онбординг пользователей на сайте',
                    'description' => 'Пошаговые подсказки к элементам страницы и интерактивные сценарии.',
                ],
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => 'Опросы и анкеты для сайта',
                    'description' => 'Пошаговые анкеты и мини-опросы для сбора ответов, контактов, вводных и обратной связи посетителя.',
                ],
            ],
        ],
    ],
];
$this->registerMetaTag([
        'name' => 'description',
        'content' => $this->params['seoDescription'],
]);
$this->registerMetaTag([
        'name' => 'keywords',
        'content' => 'обратная связь на сайт, форма обратной связи, виджет обратной связи, онлайн-поддержка на сайт, чат поддержки на сайт, кнопки обратной связи, SiteWidget',
]);
?>

<main class="site-landing">
    <section class="site-landing__hero">
        <div>
            <div class="site-landing__eyebrow">Когда посетителю нужен быстрый ответ</div>
            <h1>Виджет обратной связи для сайта</h1>
            <p class="site-landing__lead">
                SiteWidget добавляет на сайт обратную связь: форму обращения, онлайн-чат с менеджером,
                быстрые кнопки вопросов, Android-уведомления, Telegram-бота, базу знаний, анкеты и онбординг
                в одном виджете.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Начать использовать', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <a class="site-landing__button site-landing__button--ghost" href="#modules">Посмотреть модули</a>
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
                    <div>
                        <div class="site-landing__chart"></div>
                        <div class="site-landing__mini-graph" aria-hidden="true">
                            <div class="site-landing__bars">
                                <span class="site-landing__bar"></span>
                                <span class="site-landing__bar"></span>
                                <span class="site-landing__bar"></span>
                                <span class="site-landing__bar"></span>
                            </div>
                            <div class="site-landing__mini-line"></div>
                        </div>
                    </div>
                    <div>
                        <div class="site-landing__line"></div>
                        <div class="site-landing__line"></div>
                        <div class="site-landing__line"></div>
                        <div class="site-landing__line"></div>
                    </div>
                </div>
            </div>
            <div class="site-landing__widget">
                <div class="site-landing__widget-head">Онлайн-поддержка</div>
                <div class="site-landing__widget-body">
                    <div class="site-landing__message">Операторы онлайн</div>
                    <div class="site-landing__message">Чем помочь на этой странице?</div>
                </div>
            </div>
        </div>
    </section>

    <section id="modules" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Форма обратной связи, чат и подсказки в одном виджете</h2>
            <p class="site-landing__section-lead">
                SiteWidget закрывает несколько задач сразу: посетитель может написать менеджеру,
                выбрать частый вопрос, оставить контакты, открыть инструкцию из подсказки или пройти сценарий онбординга.
            </p>
            <div class="site-landing__grid">
                <article class="site-landing__card" id="support">
                    <img class="site-landing__card-icon" src="/img/sitewidget-module-support.svg" alt="">
                    <h3>Онлайн-поддержка на сайт</h3>
                    <p>Чат с оператором, форма обратной связи, быстрые кнопки тем и тикеты. Менеджеры видят горячие обращения в панели, Android-приложении и Telegram-боте.</p>
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
            <h2>Как подключается обратная связь</h2>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>01. Подключение</strong>
                    <p>Владелец сайта вставляет JS-код или подключает CMS-модуль. Виджет получает конфигурацию по public key и домену сайта.</p>
                </div>
                <div class="site-landing__step">
                    <strong>02. Настройка</strong>
                    <p>В панели владельца сайта настраиваются форма обратной связи, оформление, модули, расписание операторов, уведомления и роли.</p>
                </div>
                <div class="site-landing__step">
                    <strong>03. Работа</strong>
                    <p>Пользователь сайта получает помощь на месте, а владелец сайта видит обращения, историю диалогов и активность виджета.</p>
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
            <h2>Тарифы под рост обратной связи</h2>
            <p class="site-landing__section-lead">
                Онлайн-поддержка доступна сразу. Платные тарифы открывают больше ответов, операторов,
                дополнительные модули, а Pro — несколько проектов. Оплату подключим позже, сейчас тариф можно
                включить вручную после обращения.
            </p>

            <div class="site-landing__pricing">
                <article class="site-landing__price-card">
                    <div class="site-landing__price-head">
                        <span>Free</span>
                        <strong>0 ₽</strong>
                    </div>
                    <p>Для первого подключения и проверки виджета на сайте.</p>
                    <ul>
                        <li>50 ответов операторов в день</li>
                        <li>Пользуйся сразу после регистрации</li>
                        <li>300 диалогов и 3000 сообщений в месяц</li>
                        <li>1 проект</li>
                        <li>1 оператор</li>
                        <li>Онлайн-поддержка: чат, диалоги и история обращений</li>
                        <li>Android-приложение: уведомления о новых обращениях</li>
                        <li>Настраиваемое автооткрытие виджета</li>
                        <li>1 кнопка быстрого обращения с готовым ответом</li>
                        <li>CMS-интеграции</li>
                        <li>История 30 дней</li>
                    </ul>
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
                </article>
            </div>

            <div class="site-landing__business">
                <strong>Для высоконагруженных сайтов</strong>
                <span>
                    Можно согласовать индивидуальный лимит, отдельные условия хранения истории и расширенные настройки
                    нагрузки.
                </span><br><br>
                <strong>Тариф можно включить вручную, пока оплата на сайте готовится.</strong>
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
                <h2>SiteWidget превращает обратную связь в понятный диалог</h2>
                <p class="site-landing__section-lead">
                    Онлайн-поддержка доступна как базовый модуль. В Start добавляются инструкции, онбординг,
                    анкетирование и роли. Pro нужен для нескольких проектов и повышенных лимитов.
                </p>
                <?= Html::a('Начать использовать', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </div>
        </div>
    </section>
</main>
