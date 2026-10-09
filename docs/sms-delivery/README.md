# Ручная отправка SMS-кодов

Код создаёт и проверяет исходный проект или установленный в нём CMS-модуль. SiteWidget только доставляет задание менеджеру и открывает на его Android-телефоне стандартное приложение SMS с заполненными номером и текстом.

Основной сценарий - одноразовое подтверждение телефона при регистрации. После успешной проверки сайт сохраняет `phone_verified_at`; при обычных последующих входах новый SMS-код не требуется.

## Универсальный PHP

Подключите `php/SiteWidgetSmsClient.php` через Composer autoload или `require_once`:

```php
use SiteWidget\Sms\SiteWidgetSmsClient;

$client = new SiteWidgetSmsClient($_ENV['SITEWIDGET_SMS_TOKEN']);
$client->createTask(
    requestId: 'login-' . $userId . '-' . time(),
    phone: $phone,
    text: 'Код подтверждения ShopsBox: ' . $code,
    expiresAt: new DateTimeImmutable('+5 minutes'),
);
```

Вызов выполняется только с backend. API-токен нельзя передавать в браузер.

## Yii2

```php
'container' => [
    'definitions' => [
        SiteWidgetSmsClient::class => static fn() => new SiteWidgetSmsClient(
            $_ENV['SITEWIDGET_SMS_TOKEN'],
        ),
    ],
],
```

После этого клиент можно принимать в конструкторе controller/service через DI.

## Laravel

```php
// AppServiceProvider::register()
$this->app->singleton(SiteWidgetSmsClient::class, static fn() => new SiteWidgetSmsClient(
    config('services.sitewidget.sms_token'),
));
```

После регистрации используйте constructor injection в controller/service.

## Callback

SiteWidget подписывает сырое тело запроса HMAC SHA-256 и передаёт подпись в `X-SiteWidget-Signature`. Проверяйте её до разбора JSON:

```php
$rawBody = file_get_contents('php://input');
$valid = SiteWidgetSmsClient::verifyCallback(
    $rawBody,
    $_SERVER['HTTP_X_SITEWIDGET_SIGNATURE'] ?? '',
    $_ENV['SITEWIDGET_SMS_CALLBACK_SECRET'],
);
```

Статусы первой версии: `queued`, `notified`, `sms_opened`, `expired`, `failed`. `sms_opened` означает, что SiteWidget открыл системный редактор SMS. Фактическую отправку SiteWidget не подтверждает.

## Пример ShopsBox на Symfony

- `examples/SymfonySmsAuthController.php` - безопасные endpoint запроса и проверки кода.
- `examples/symfony-config.yaml` - rate limiter и DI.
- `examples/shopsbox-button.js` - обработчик кнопки на странице.

Браузер обращается только к backend ShopsBox. Секретный токен и сам вызов SiteWidget остаются на сервере.
