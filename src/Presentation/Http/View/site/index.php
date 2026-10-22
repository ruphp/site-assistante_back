<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->registerCssFile('/css/home.css', ['depends' => [\app\Presentation\Yii\Asset\AppAsset::class]]);

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

<main class="site-landing sw-home">
    <section class="sw-home__hero">
        <div class="sw-home__intro">
            <p class="sw-home__brand"><img src="/img/sitewidget-logo.svg" width="36" height="40" alt=""> SiteWidget</p>
            <h1>Онлайн чат для сайта</h1>
            <p class="sw-home__headline">Посетитель пишет на сайте.<br>Вы отвечаете из Android или Telegram.</p>
            <p class="site-landing__lead">
                Не нужно держать сайт открытым. Приложение уведомит о новом вопросе
                и напомнит, если посетитель ещё ждёт ответа.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Начать бесплатно', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <button class="site-landing__button site-landing__button--ghost" type="button" data-sitewidget-open-support>Открыть живой чат</button>
            </div>
            <p class="sw-home__free-note">Есть бесплатный тариф. Без привязки карты.</p>
        </div>

        <figure class="sw-home__conversation" aria-label="Пример общения посетителя сайта с менеджером">
            <div class="sw-home__visitor">
                <div class="sw-home__surface-label">На вашем сайте</div>
                <div class="sw-home__chat">
                    <div class="sw-home__chat-header"><img src="/img/sitewidget-module-support.svg" width="27" height="27" alt=""><div><strong>Онлайн консультант</strong><span><i></i> На связи</span></div></div>
                    <div class="sw-home__chat-body">
                        <p class="sw-home__bubble sw-home__bubble--visitor">Здравствуйте! Можно заказать доставку на завтра?<small>12:40</small></p>
                        <p class="sw-home__bubble sw-home__bubble--reply">Да, конечно. Подскажите ваш город - проверю время доставки.<small>Менеджер · 12:41</small></p>
                    </div>
                    <button type="button" class="sw-home__try-chat" data-sitewidget-open-support>Задать свой вопрос <span aria-hidden="true">↗</span></button>
                </div>
            </div>
            <div class="sw-home__connection" aria-hidden="true"><span>Тот же диалог</span>↔</div>
            <div class="sw-home__manager">
                <div class="sw-home__surface-label">У менеджера в Android</div>
                <div class="sw-home__phone">
                    <div class="sw-home__phone-head"><img src="/img/sitewidget-logo.svg" width="26" height="29" alt=""><strong>SiteWidget Менеджер</strong><span aria-hidden="true">•••</span></div>
                    <div class="sw-home__phone-toolbar">Диалог с посетителем <span>Ваш сайт</span></div>
                    <div class="sw-home__phone-message"><strong>Посетитель</strong><p>Здравствуйте! Можно заказать доставку на завтра?</p><small>12:40</small></div>
                    <div class="sw-home__phone-reply">Да, конечно. Подскажите ваш город - проверю время доставки.<small>Вы · 12:41</small></div>
                    <div class="sw-home__phone-compose">Ответ посетителю<span aria-hidden="true">➤</span></div>
                </div>
            </div>
            <figcaption>Пример диалога. Сообщения с сайта приходят в приложение менеджера, ответы возвращаются в виджет.</figcaption>
        </figure>
    </section>

    <section id="modules" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Сначала живой ответ.<br>Остальное - когда понадобится.</h2>
            <p class="site-landing__section-lead">
                Онлайн чат открывается первым. А инструкции, подсказки и анкеты помогают посетителю решить часть вопросов самостоятельно.
            </p>
            <div class="site-landing__grid">
                <article class="site-landing__card" id="support">
                    <img class="site-landing__card-icon" src="/img/sitewidget-module-support.svg" alt="">
                    <h3>Онлайн консультант для сайта</h3>
                    <p>Общение с менеджером прямо на странице. История диалогов сохраняется, а быстрые кнопки помогают сразу выбрать нужную тему.</p>
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
                    <p>Покажите, где оформить заказ или настроить аккаунт. Подсказка появляется рядом с нужной кнопкой, а не на отдельной странице.</p>
                    <?= Html::a('Подробнее об онбординге', ['/onboarding'], ['class' => 'site-landing__link']) ?>
                </article>
                <article class="site-landing__card">
                    <img class="site-landing__card-icon" src="/img/sitewidget-checkbox.svg" alt="">
                    <h3>Опросы и анкеты</h3>
                    <p>Уточните задачу, соберите контакты или спросите мнение. Посетитель заполняет форму внутри виджета и может вернуться к ней позже.</p>
                    <?= Html::a('Подробнее об анкетах', ['/surveys'], ['class' => 'site-landing__link']) ?>
                </article>
            </div>
        </div>
    </section>

    <section id="how" class="site-landing__section">
        <div class="site-landing__inner">
            <h2>Виджет обратной связи для сайта: от вопроса до ответа</h2>
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
                    <p class="sw-home__kicker">SiteWidget для Android</p>
                    <h2>Один сигнал легко пропустить.<br>Поэтому есть напоминания.</h2>
                    <p class="site-landing__section-lead">
                        В активном режиме приложение повторяет уведомления о неотвеченных диалогах.
                        В пассивном - сообщает о новых сообщениях. Вы выбираете подходящий режим.
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
                <li>Переписка с посетителем прямо в Telegram.</li>
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
                <li>Передача ролей пользователей для фильтрации контента модулей в тарифе Pro.</li>
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
            <h2>Подтверждение номера телефона через SMS</h2>
            <p class="site-landing__section-lead">
                SiteWidget помогает небольшому проекту подтвердить номер пользователя без отдельного SMS-шлюза.
                Это можно использовать при регистрации, оформлении заявки или в любой другой форме.
                Сообщение отправляет владелец сайта или менеджер через Android-приложение. Для отправки используются SMS его мобильного тарифа.
            </p>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>Сайт запрашивает подтверждение</strong>
                    <p>Пользователь вводит номер в форме на сайте. Сервер сайта передаёт его и готовый текст SMS в Android-приложение SiteWidget. Запрос отправляется по защищённой связи между серверами.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Владелец сайта или менеджер отправляет SMS</strong>
                    <p>Android-приложение открывает готовое SMS. Вы видите текст с кодом и номер получателя, затем сами нажимаете «Отправить».</p>
                </div>
                <div class="site-landing__step">
                    <strong>Проверку выполняет ваш сайт</strong>
                    <p>После получения SMS пользователь сайта вводит код. Ваш проект проверяет его и разрешает регистрацию либо запускает любой другой предусмотренный вами сценарий.</p>
                </div>
            </div>
            <p class="sw-home__sms-note">Доступно и в Free. SMS отправляются вручную и расходуются по мобильному тарифу отправителя.</p>
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
                    <p>Принимайте обращения с сайта и публикуйте готовые ответы на частые вопросы бесплатно.</p>
                    <ul>
                        <li>Онлайн чат, диалоги и история обращений</li>
                        <li>Инструкции: разделы и статьи</li>
                        <li>Быстрая кнопка с автоответом или переходом к инструкции</li>
                        <li>Общение менеджеров с пользователями сайта прямо из Android-приложения и Telegram</li>
                        <li>Подтверждение номеров пользователей по SMS без отдельного SMS-сервиса</li>
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
                    <p>Снижайте число повторных вопросов и помогайте посетителям быстрее находить нужное.</p>
                    <ul>
                        <li>Все возможности Free</li>
                        <li>Поиск по инструкциям и вложенные разделы</li>
                        <li>Модуль онбординга: подсказки и сценарии обучения навигации по сайту</li>
                        <li>Анкетирование: формы, вопросы и сбор данных</li>
                        <li>Несколько быстрых кнопок с автоответами и полезными инструкциями</li>
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
                    <p>Управляйте поддержкой нескольких сайтов и показывайте каждой группе пользователей подходящий контент.</p>
                    <ul>
                        <li>Все возможности Start</li>
                        <li>Единое управление поддержкой нескольких сайтов</li>
                        <li>Совместная работа команды менеджеров</li>
                        <li>Передача виджету ролей авторизованных пользователей сайта для фильтрации контента модулей</li>
                    </ul>
                    <div class="site-landing__price-action">
                        <button class="site-landing__button site-landing__button--ghost" type="button" data-sitewidget-open-support>Обсудить Pro</button>
                    </div>
                </article>
            </div>

            <div class="site-landing__business">
                <strong>Для высоконагруженных сайтов</strong>
                <span>
                    Подберём конфигурацию под большой поток обращений, команду менеджеров и несколько сайтов.
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
                <h2>Посетитель уже на сайте.<br>Будьте на связи.</h2>
                <p class="site-landing__section-lead">
                    Создайте проект, установите код или CMS-модуль и проведите первый тестовый диалог.
                </p>
                <?= Html::a('Создать проект', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </div>
        </div>
    </section>
</main>
