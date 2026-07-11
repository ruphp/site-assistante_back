<?php

use yii\helpers\Html;

/**
 * @var int $publicKey
 * @var \app\Application\Panel\Dto\ClientProjectView[] $projects
 * @var \app\Application\Panel\Dto\ClientProjectView $activeProject
 */

$this->title = 'Инструкции SiteWidget';
?>

<div class="uk-container uk-position-relative">
    <?= $this->render('_projectTabs', compact('projects', 'activeProject')) ?>

    <h1>Инструкции</h1>
    <p class="uk-text-muted">
        Текущий проект: <strong><?= Html::encode($activeProject->name) ?></strong>,
        public key: <code><?= Html::encode((string)$publicKey) ?></code>
    </p>

    <div class="uk-margin uk-flex uk-flex-wrap uk-grid-small" uk-grid>
        <div><?= Html::a('Подключение', '#connect', ['class' => 'uk-button uk-button-default']) ?></div>
        <div><?= Html::a('Android', '#android', ['class' => 'uk-button uk-button-default']) ?></div>
        <div><?= Html::a('Telegram', '#telegram', ['class' => 'uk-button uk-button-default']) ?></div>
        <div><?= Html::a('Онлайн-поддержка', '#support', ['class' => 'uk-button uk-button-default']) ?></div>
    </div>

    <section id="connect" class="uk-card uk-card-default uk-card-body uk-margin">
        <h2>Как подключить виджет</h2>
        <ol>
            <li>Откройте раздел <?= Html::a('Параметры', ['/manager/params']) ?>.</li>
            <li>Укажите домен сайта и сохраните настройки.</li>
            <li>Скопируйте код подключения с актуальным public key проекта.</li>
            <li>Разместите код на страницах сайта перед закрывающим тегом <code>&lt;/body&gt;</code> или <code>&lt;/head&gt;</code>.</li>
        </ol>

        <h3>Передача пользователя сайта</h3>
        <p>Если сайт знает авторизованного пользователя, можно передать его данные в блоке <code>_user</code>.</p>
        <pre><code>_user: {
    id: 1234,
    role: [4, 5],
    name: 'Имя пользователя',
    email: 'user@example.ru'
}</code></pre>

        <p class="uk-text-muted">
            Роли нужны для будущей фильтрации контента. Если пользователь не авторизован, оставьте значения пустыми.
        </p>

        <h3>Обновление на динамических страницах</h3>
        <p>Если сайт подгружает блоки без перезагрузки страницы, вызовите обновление виджета:</p>
        <pre><code>window.SiteWidget.api.update();</code></pre>
    </section>

    <section id="android" class="uk-card uk-card-default uk-card-body uk-margin">
        <h2>Как установить Android-приложение</h2>
        <p>
            Приложение нужно менеджеру, чтобы получать push-уведомления о новых обращениях и неотвеченных диалогах.
        </p>
        <p>
            <?= Html::a('Скачать SiteWidget Менеджер APK', ['/sitewidgetmanager.apk'], [
                'class' => 'uk-button uk-button-primary',
                'download' => true,
            ]) ?>
        </p>

        <div class="uk-child-width-1-2@m uk-grid-match" uk-grid>
            <div>
                <div class="uk-card uk-card-muted uk-card-body">
                    <h3>1. Скачайте APK</h3>
                    <p>Откройте ссылку с телефона. Если Android предложит проверку приложения, запустите её.</p>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-muted uk-card-body">
                    <h3>2. Разрешите установку</h3>
                    <p>Если установка из браузера отключена, Android попросит разрешить установку из неизвестного источника.</p>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-muted uk-card-body">
                    <h3>3. Войдите в аккаунт</h3>
                    <p>Используйте email/пароль владельца или менеджера. После входа разрешите уведомления.</p>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-muted uk-card-body">
                    <h3>4. Работайте с диалогами</h3>
                    <p>Открывайте новые обращения, отвечайте посетителям и следите за активным/пассивным режимом уведомлений.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="telegram" class="uk-card uk-card-default uk-card-body uk-margin">
        <h2>Как подключить Telegram-бота</h2>
        <p>
            Telegram-бот работает как лёгкая операторская: показывает новые обращения, позволяет ответить посетителю и владельцу закрыть диалог.
        </p>
        <p>
            <?= Html::a('Открыть @SiteWidgetBot', 'https://t.me/SiteWidgetBot', [
                'class' => 'uk-button uk-button-primary',
                'target' => '_blank',
                'rel' => 'noopener',
            ]) ?>
            <?= Html::a('Перейти к менеджерам', ['/manager/operators'], ['class' => 'uk-button uk-button-default']) ?>
        </p>
        <ol>
            <li>Откройте раздел <strong>Менеджеры</strong>.</li>
            <li>Скопируйте код Telegram-бота для нужного менеджера.</li>
            <li>Откройте <strong>@SiteWidgetBot</strong> и отправьте команду <code>/start CODE</code>.</li>
            <li>Команда <code>/dialogs</code> покажет открытые горячие диалоги.</li>
        </ol>
        <p class="uk-text-muted">
            Telegram показывает только открытые диалоги. Архив и закрытые обращения смотрите в панели управления.
        </p>
    </section>

    <section id="support" class="uk-card uk-card-default uk-card-body uk-margin">
        <h2>Модуль онлайн-поддержки и быстрые кнопки</h2>
        <ul>
            <li><strong>Настройки</strong> - приветствие, расписание операторов, автооткрытие, контакты посетителя и уведомления.</li>
            <li><strong>Кнопки быстрых обращений</strong> - темы вроде “Не пришёл товар” или “Хочу подключить тариф”.</li>
            <li><strong>Готовый ответ</strong> - бот сразу показывает посетителю заготовленный ответ, но менеджер не получает лишнее уведомление, пока посетитель не продолжит вопрос.</li>
            <li><strong>Уточняющий вопрос</strong> - кнопка создаёт диалог и показывает посетителю вопрос от менеджера.</li>
            <li><strong>Лимиты</strong> - ответы операторов расходуются всеми менеджерами одного проекта одновременно.</li>
        </ul>
    </section>
</div>
