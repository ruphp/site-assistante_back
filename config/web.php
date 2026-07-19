<?php


$url_rules = [
//////  общее
    [
        'pattern' => '/',
        'route' => 'site',
    ],

    [
        'pattern' => '/login',
        'route' => 'site/login',
    ],

    [
        'pattern' => 'site/login',
        'route' => 'site/login',
    ],

    [
        'pattern' => '/join',
        'route' => 'site/join',
    ],
    [
        'pattern' => '/cms-plugins',
        'route' => 'site/cms-plugins',
    ],
    [
        'pattern' => '/faq-instructions',
        'route' => 'site/faq-instructions',
    ],
    [
        'pattern' => '/onboarding',
        'route' => 'site/onboarding',
    ],
    [
        'pattern' => '/surveys',
        'route' => 'site/surveys',
    ],
    [
        'pattern' => '/instructions',
        'route' => 'site/instructions',
    ],

    [
        'pattern' => '/confirm-email',
        'route' => 'site/confirm-email',
    ],
    [
        'pattern' => '/site/yandex-mobile-callback',
        'route' => 'site/yandex-mobile-callback',
    ],

    [
        'pattern' => '/logout',
        'route' => 'site/logout',
    ],

    [
        'pattern' => '/send-email',
        'route' => 'site/send-email',
    ],
//////  Панель управления
    [
        'pattern' => '/manager',
        'route' => 'manager/panel/index',
    ],
    [
        'pattern' => '/manager/project-create',
        'route' => 'manager/panel/project-create',
    ],

    [
        'pattern' => '/manager/designe',
        'route' => 'manager/panel/designe',
    ],

    [
        'pattern' => '/manager/params',
        'route' => 'manager/panel/params',
    ],

    [
        'pattern' => '/manager/statistics',
        'route' => 'manager/panel/statistics',
    ],
    [
        'pattern' => '/manager/limits',
        'route' => 'manager/panel/limits',
    ],
    [
        'pattern' => '/manager/operators',
        'route' => 'manager/panel/operators',
    ],
    [
        'pattern' => '/manager/instructions',
        'route' => 'manager/instructions/index',
    ],
    [
        'pattern' => '/manager/instructions/sections',
        'route' => 'manager/instructions/sections',
    ],
    [
        'pattern' => '/manager/instructions/create',
        'route' => 'manager/instructions/create',
    ],
    [
        'pattern' => '/manager/instructions/update',
        'route' => 'manager/instructions/update',
    ],
    [
        'pattern' => '/manager/instructions/stats',
        'route' => 'manager/instructions/stats',
    ],
    [
        'pattern' => '/manager/instructions/category-delete',
        'route' => 'manager/instructions/delete-category',
    ],
    [
        'pattern' => '/manager/instructions/article-delete',
        'route' => 'manager/instructions/delete-article',
    ],
    [
        'pattern' => '/manager/onboarding',
        'route' => 'manager/onboarding/index',
    ],
    [
        'pattern' => '/manager/onboarding/create',
        'route' => 'manager/onboarding/create',
    ],
    [
        'pattern' => '/manager/onboarding/update',
        'route' => 'manager/onboarding/update',
    ],
    [
        'pattern' => '/manager/onboarding/delete',
        'route' => 'manager/onboarding/delete',
    ],
    [
        'pattern' => '/manager/onboarding/sections',
        'route' => 'manager/onboarding/sections',
    ],
    [
        'pattern' => '/manager/onboarding/section-delete',
        'route' => 'manager/onboarding/section-delete',
    ],
    [
        'pattern' => '/manager/onboarding/steps',
        'route' => 'manager/onboarding/steps',
    ],
    [
        'pattern' => '/manager/onboarding/step-delete',
        'route' => 'manager/onboarding/step-delete',
    ],
    [
        'pattern' => '/manager/onboarding/hints',
        'route' => 'manager/onboarding/hints',
    ],
    [
        'pattern' => '/manager/onboarding/hint-create',
        'route' => 'manager/onboarding/hint-create',
    ],
    [
        'pattern' => '/manager/onboarding/hint-update',
        'route' => 'manager/onboarding/hint-update',
    ],
    [
        'pattern' => '/manager/onboarding/hint-delete',
        'route' => 'manager/onboarding/hint-delete',
    ],
    [
        'pattern' => '/manager/onboarding/selector-session',
        'route' => 'manager/onboarding/selector-session',
    ],
    [
        'pattern' => '/manager/onboarding/selector-status',
        'route' => 'manager/onboarding/selector-status',
    ],
    [
        'pattern' => '/manager/profile',
        'route' => 'manager/panel/profile',
    ],
    [
        'pattern' => '/manager/operator/owner-contacts',
        'route' => 'manager/panel/operator-owner-contacts',
    ],
    [
        'pattern' => '/manager/operator/projects',
        'route' => 'manager/panel/operator-projects',
    ],
    [
        'pattern' => '/manager/operator/reset-password',
        'route' => 'manager/panel/operator-reset-password',
    ],
    [
        'pattern' => '/manager/operator/disable',
        'route' => 'manager/panel/operator-disable',
    ],
    [
        'pattern' => '/manager/support',
        'route' => 'manager-support/index',
    ],
    [
        'pattern' => '/manager/support/conversations',
        'route' => 'manager-support/conversations',
    ],
    [
        'pattern' => '/manager/support/entry-points',
        'route' => 'manager-support/entry-points',
    ],
    [
        'pattern' => '/manager/support/entry-point/delete',
        'route' => 'manager-support/entry-point-delete',
    ],
    [
        'pattern' => '/manager/support/conversation',
        'route' => 'manager-support/conversation',
    ],
    [
        'pattern' => '/manager/support/conversation-close',
        'route' => 'manager-support/conversation-close',
    ],
    [
        'pattern' => '/manager/support/conversation-delete',
        'route' => 'manager-support/conversation-delete',
    ],
    [
        'pattern' => '/manager/support/reply',
        'route' => 'manager-support/reply',
    ],
    [
        'pattern' => '/manager/support/ws-token',
        'route' => 'manager-support/ws-token',
    ],

    [
        'pattern' => '/manager/roles',
        'route' => 'manager/panel/roles',
    ],

    [
        'pattern' => '/manager/role/delete',
        'route' => 'manager/panel/role-delete',
    ],

    [
        'pattern' => '/manager/export/xls',
        'route' => 'manager/xls/index',
    ],
//////  Админка


    [
        'pattern' => '/admin',
        'route' => 'admin/panel/index',
    ],

    [
        'pattern' => '/admin/clients',
        'route' => 'admin/panel/clients',
    ],
    [
        'pattern' => '/admin/clients/limits',
        'route' => 'admin/panel/limits',
    ],
    [
        'pattern' => '/admin/clients/limits/reset-daily-replies',
        'route' => 'admin/panel/reset-daily-replies',
    ],
    [
        'pattern' => '/admin/instructions',
        'route' => 'admin/panel/instructions',
    ],
    [
        'pattern' => '/admin/instructions/block',
        'route' => 'admin/panel/toggle-instruction-block',
    ],
    [
        'pattern' => '/admin/instructions/view',
        'route' => 'admin/panel/instruction-view',
    ],
    [
        'pattern' => '/admin/instructions/creation',
        'route' => 'admin/panel/toggle-instruction-creation',
    ],

    [
        'pattern' => '/admin/clients/join',
        'route' => 'admin/panel/join',
    ],

    [
        'pattern' => '/admin/clients/update',
        'route' => 'admin/panel/update',
    ],

    [
        'pattern' => '/admin/clients/view',
        'route' => 'admin/panel/view',
    ],

    [
        'pattern' => '/admin/clients/dialogs',
        'route' => 'admin/panel/dialogs',
    ],

    [
        'pattern' => '/admin/clients/dialog',
        'route' => 'admin/panel/dialog',
    ],

    [
        'pattern' => '/admin/clients/delete',
        'route' => 'admin/panel/delete',
    ],

    [
        'pattern' => '/admin/statistics',
        'route' => 'admin/panel/statistics',
    ],

    [
        'pattern' => '/admin/grafana',
        'route' => 'admin/panel/grafana',
    ],

    [
        'pattern' => '/admin/content_statistics',
        'route' => 'admin/panel/content-statistics',
    ],

    [
        'pattern' => '/admin/content_statistics/chart',
        'route' => 'admin/panel/chart',
    ],

    [
        'pattern' => '/admin/usage/chart',
        'route' => 'admin/panel/chart',
    ],

    [
        'pattern' => '/admin/statistics/chart',
        'route' => 'admin/panel/chart',
    ],
///// API
    [
        'pattern' => '/api/configuration',
        'route' => 'api/widget/configuration',
    ],
    [
        'pattern' => '/api/open_log',
        'route' => 'api/widget/log-open',
    ],
    [
        'pattern' => '/api/instructions',
        'route' => 'api/instructions/instructions',
    ],
    [
        'pattern' => '/api/instruction',
        'route' => 'api/instructions/instruction',
    ],
    [
        'pattern' => '/api/instructions/search',
        'route' => 'api/instructions/search',
    ],
    [
        'pattern' => '/api/instructions/linktag',
        'route' => 'api/instructions/linktag',
    ],
    [
        'pattern' => '/api/instruction_favorites',
        'route' => 'api/instructions/favorites',
    ],
    [
        'pattern' => '/api/instruction_estimate',
        'route' => 'api/instructions/instruction-estimate',
    ],
    [
        'pattern' => '/api/log_instruction_interest',
        'route' => 'api/instructions/log-interest',
    ],
    [
        'pattern' => '/api/hints',
        'route' => 'api/onboarding/hints',
    ],
    [
        'pattern' => '/api/onboardings',
        'route' => 'api/onboarding/onboardings',
    ],
    [
        'pattern' => '/api/allonboardings',
        'route' => 'api/onboarding/allonboardings',
    ],
    [
        'pattern' => '/api/tooltip',
        'route' => 'api/onboarding/tooltip',
    ],
    [
        'pattern' => '/api/onboarding_log',
        'route' => 'api/onboarding/onboarding-log',
    ],
    [
        'pattern' => '/api/onboarding/selector-complete',
        'route' => 'api/onboarding/selector-complete',
    ],
    [
        'pattern' => '/api/support/state',
        'route' => 'api/support/state',
    ],
    [
        'pattern' => '/api/support/conversation/start',
        'route' => 'api/support/start-conversation',
    ],
    [
        'pattern' => '/api/support/message/send',
        'route' => 'api/support/send-message',
    ],
    [
        'pattern' => '/api/support/messages',
        'route' => 'api/support/messages',
    ],
    [
        'pattern' => '/api/support/conversation/close',
        'route' => 'api/support/close-conversation',
    ],
    [
        'pattern' => '/api/support/conversation/verify',
        'route' => 'api/support/verify-conversation',
    ],
    [
        'pattern' => '/api/support/conversation/activity',
        'route' => 'api/support/activity',
    ],
    [
        'pattern' => '/api/report/usage',
        'route' => 'api/report/usage',
    ],
    [
        'pattern' => '/manager/export/xls/usage',
        'route' => 'api/xls/usage',
    ],

    [//todo времянка
        'route' => 'test/reply',
        'pattern' => 'api/reply',
    ],

    [//todo времянка
        'route' => 'api/systems',
        'pattern' => '/systems',
    ],
    'POST api/auth/login' => 'api/auth/login',
    'POST api/auth/yandex-url' => 'api/auth/yandex-url',
    'POST api/auth/yandex' => 'api/auth/yandex',
    'POST api/telegram/manager/webhook' => 'api/telegram-manager-bot/webhook',
    'GET api/support/manager/conversations' => 'api/support-manager/conversations',
    'GET api/support/manager/messages' => 'api/support-manager/messages',
    'POST api/support/manager/send-message' => 'api/support-manager/send-message',
    'POST api/support/manager/device-token' => 'api/support-manager/device-token',
    'POST api/support/manager/device-token/delete' => 'api/support-manager/device-token-delete',

];


$authClients = [];

if (!empty($_ENV['YANDEX_OAUTH_CLIENT_ID'] ?? '') && !empty($_ENV['YANDEX_OAUTH_CLIENT_SECRET'] ?? '')) {
    $authClients['yandex'] = [
        'class' => 'yii\authclient\clients\Yandex',
        'clientId' => $_ENV['YANDEX_OAUTH_CLIENT_ID'],
        'clientSecret' => $_ENV['YANDEX_OAUTH_CLIENT_SECRET'],
        'scope' => 'login:info login:email',
        'validateAuthState' => false,
        'normalizeUserAttributeMap' => [
            'email' => function ($attributes) {
                return $attributes['email']
                    ?? $attributes['default_email']
                    ?? current($attributes['emails'] ?? [])
                    ?: null;
            },
        ],
    ];
}


$config = [

    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\Presentation\Http\Controller',
    'viewPath' => '@app/src/Presentation/Http/View',
    'container' => require __DIR__ . '/container.php',
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'layout' => 'smartius',
    'name' => 'SiteWidget',
    'aliases' => [
        //'@bower' => '@vendor/yidas/yii2-bower-asset/bower',
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [],
    'controllerMap' => [
        'manager-support' => [
            'class' => app\Modules\Support\Presentation\Http\Controller\ManagerSupportController::class,
        ],
        'api/telegram-manager-bot' => [
            'class' => app\Presentation\Http\Controller\api\TelegramManagerBotController::class,
        ],
    ],
    'components' => [
        'request' => [
            'enableCsrfValidation' => true,
            'cookieValidationKey' => $_ENV['COOKIE_VALIDATION_KEY'],
            'baseUrl' => '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            //'class' => YII_ENV_DEV ? 'yii\caching\DummyCache' : 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\Infrastructure\User\UserIdentity',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapPluginAsset' => [
                    // 'js'=>[]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    //'css' => [],
                ],

            ],
            'linkAssets' => false,
        ],
        'mailer' => [
            'class' => '\yii\symfonymailer\Mailer',
            'transport' => [
                'scheme' => 'smtps',
                'host' => $_ENV['MAIL_HOST'],
                'username' => $_ENV['MAIL_USER'],
                'password' => $_ENV['MAIL_PASS'],
                'port' => 465,
                'options' => ['ssl' => true],
            ],
            'viewPath' => '@app/src/Presentation/Mail/View',
            'useFileTransport' => false,
            'messageConfig' => [
                'from' => [$_ENV['MAIL_USER'] => 'SiteWidget'],
            ],
        ],
        'log' => [
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'driverName' => $_ENV['DB_DIVER_NAME'],
            'dsn' => 'pgsql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'emulatePrepare' => (bool)$_ENV['DB_EMULATE_PREPARE'],
            'enableSchemaCache' => YII_ENV_DEV ? false : true,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'database' => 0,
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => $authClients,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'suffix' => '',
            'rules' => $url_rules,

        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'ru_RU',
                    'fileMap' => [
                        'app/auth' => 'auth.php',
                    ],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
    ],
    'params' => [
        'adminEmail' => $_ENV['ADMIN_EMAIL'] ?: $_ENV['MAIL_USER'],
        'language' => 'ru-RU',
        'sourceLanguage' => 'ru-RU',
    ],
];
// получаем список директорий в protected/modules
$modulesPath = dirname(__FILE__) . '/../modules';

if (is_dir($modulesPath)) {
    $dirs = scandir($modulesPath);

    foreach ($dirs as $val) {
        if ($val[0] != '.') {
            $config['bootstrap'][] = $val;
            $config['modules'][$val] = ['class' => 'app\modules\\' . $val . '\Module'];
            if ($val == 'chatbots') {
                // получаем список директорий в подмодулях чатбота
                $chatbotModulesPath = $modulesPath . '/chatbots/modules';

                if (!is_dir($chatbotModulesPath)) {
                    continue;
                }

                $child_dirs = scandir($chatbotModulesPath);
                foreach ($child_dirs as $child_val) {
                    if ($child_val[0] != '.') {
                        $config['bootstrap'][] = $child_val;
                        $config['modules'][$child_val] = ['class' => 'app\modules\chatbots\modules\\' . $child_val . '\Module'];
                    }
                }
            }
        }
    }
}


/*if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '*'],
    ];
}*/
return $config;
