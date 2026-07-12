<?php

use yii\helpers\Html;

$this->title = 'Инструкции SiteWidget';
?>

<div class="uk-container uk-position-relative">
    <h1>Инструкции</h1>

    <div class="uk-margin uk-flex uk-flex-wrap uk-grid-small" uk-grid>
        <div><?= Html::a('Подключение', '#connect', ['class' => 'uk-button uk-button-default']) ?></div>
        <div><?= Html::a('Android', '#android', ['class' => 'uk-button uk-button-default']) ?></div>
        <div><?= Html::a('Telegram', '#telegram', ['class' => 'uk-button uk-button-default']) ?></div>
        <div><?= Html::a('Онлайн-поддержка', '#support', ['class' => 'uk-button uk-button-default']) ?></div>
    </div>

    <section id="connect" class="uk-card uk-card-default uk-card-body uk-margin">
        <h2>Как подключить виджет вручную</h2>
        <p class="uk-text-muted">
            Этот способ нужен, если для сайта нет готовой CMS-интеграции или вы хотите вставить код самостоятельно.
        </p>
        <ol>
            <li>Откройте <?= Html::a('Мои проекты', ['/manager']) ?> и в карточке нужного проекта нажмите <strong>Параметры</strong>.</li>
            <li>Скопируйте код подключения с public key проекта.</li>
            <li>Разместите код на страницах сайта перед закрывающим тегом <code>&lt;/body&gt;</code> или <code>&lt;/head&gt;</code>.</li>
        </ol>

        <h3>Передача пользователя и ролей</h3>
        <p>
            Если сайт знает авторизованного пользователя, передавайте его данные в блоке <code>_user</code>.
            Это работает и для ручного подключения, и для CMS-модулей.
        </p>
        <pre><code>_user: {
    id: 1234,
    name: 'Имя пользователя',
    email: 'user@example.ru',
    role: [4, 5]
}</code></pre>
        <ul>
            <li><strong>id, name, email</strong> можно передавать в базовой версии.</li>
            <li><strong>role</strong> используется для дополнительной фильтрации контента и доступна в платных тарифах.</li>
        </ul>

        <h3>Где взять public key</h3>
        <p>Откройте <?= Html::a('Мои проекты', ['/manager']) ?>. Public key указан в карточке проекта и вверху формы подключения внутри раздела <strong>Параметры</strong>.</p>

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
                    <?= Html::img('/img/android-install/check.webp', [
                        'alt' => 'Проверка приложения Android',
                        'class' => 'uk-width-1-1',
                    ]) ?>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-muted uk-card-body">
                    <h3>2. Разрешите установку</h3>
                    <p>Если установка из браузера отключена, Android попросит разрешить установку из неизвестного источника.</p>
                    <?= Html::img('/img/android-install/checking.webp', [
                        'alt' => 'Проверка Google Play Защиты',
                        'class' => 'uk-width-1-1',
                    ]) ?>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-muted uk-card-body">
                    <h3>3. Войдите в аккаунт</h3>
                    <p>Используйте email/пароль владельца или менеджера. После входа разрешите уведомления.</p>
                    <?= Html::img('/img/android-install/safe.webp', [
                        'alt' => 'Приложение безопасно',
                        'class' => 'uk-width-1-1',
                    ]) ?>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-muted uk-card-body">
                    <h3>4. Работайте с диалогами</h3>
                    <p>Открывайте новые обращения, отвечайте посетителям и следите за активным/пассивным режимом уведомлений.</p>
                    <?= Html::img('/img/android-install/installed.webp', [
                        'alt' => 'Приложение установлено',
                        'class' => 'uk-width-1-1',
                    ]) ?>
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
        <p>Если нужно открыть бота сразу с командой, используйте ссылку из таблицы менеджеров рядом с кодом активации.</p>
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
