<?php



$url_rules=[
//////  общее
        [
            'pattern' => '/',
            'route'   => 'site',
        ],

        [
            'pattern' => '/login',
            'route'   => 'site',
        ],

        [
            'pattern' => 'site/login',
            'route'   => 'site',
        ],

        [
            'pattern' => '/join',
            'route'   => 'site/join',
        ],

        [
            'pattern' => '/logout',
            'route'   => 'site/logout',
        ],

        [
            'pattern' => '/send-email',
            'route'   => 'site/send-email',
        ],
//////  Панель управления
        [
            'pattern' => '/manager',
            'route'   => 'manager/panel/index',
        ],

        [
            'pattern' => '/manager/designe',
            'route'   => 'manager/panel/designe',
        ],

        [
            'pattern' => '/manager/params',
            'route'   => 'manager/panel/params',
        ],

        [
            'pattern' => '/manager/statistics',
            'route'   => 'manager/panel/statistics',
        ],

        [
            'pattern' => '/manager/roles',
            'route'   => 'manager/panel/roles',
        ],

        [
            'pattern' => '/manager/role/delete',
            'route'   => 'manager/panel/role-delete',
        ],

        [
            'pattern' => '/manager/export/xls',
            'route'   => 'manager/xls/index',
        ],
//////  Админка


        [
            'pattern' => '/admin',
            'route'   => 'admin/panel/index',
        ],

        [
            'pattern' => '/admin/clients',
            'route'   => 'admin/panel/clients',
        ],

        [
            'pattern' => '/admin/clients/join',
            'route'   => 'admin/panel/join',
        ],

        [
            'pattern' => '/admin/clients/update',
            'route'   => 'admin/panel/update',
        ],

        [
            'pattern' => '/admin/clients/delete',
            'route'   => 'admin/panel/delete',
        ],

        [
            'pattern' => '/admin/statistics',
            'route'   => 'admin/panel/statistics',
        ],

        [
            'pattern' => '/admin/grafana',
            'route'   => 'admin/panel/grafana',
        ],

        [
            'pattern' => '/admin/content_statistics',
            'route'   => 'admin/panel/content-statistics',
        ],

        [
            'pattern' => '/admin/content_statistics/chart',
            'route'   => 'admin/panel/chart',
        ],

        [
            'pattern' => '/admin/usage/chart',
            'route'   => 'admin/panel/chart',
        ],

        [
            'pattern' => '/admin/statistics/chart',
            'route'   => 'admin/panel/chart',
        ],
///// API
        [
            'pattern' => '/api/configuration',
            'route'   => 'api/widget/configuration',
        ],
        [
            'pattern' => '/api/open_log',
            'route'   => 'api/widget/log-open',
        ],
        [
            'pattern' => '/api/report/usage',
            'route'   => 'api/report/usage',
        ],
        [
            'pattern' => '/manager/export/xls/usage',
            'route'   => 'api/xls/usage',
        ],

        [//todo времянка
            'route'   => 'test/reply',
            'pattern' => 'api/reply',
        ],

        [//todo времянка
            'route'   => 'api/systems',
            'pattern' => '/systems',
        ],


];






$config = [

    'id'         => 'basic',
    'basePath'   => dirname(__DIR__),
    'controllerNamespace' => 'app\Presentation\Http\Controller',
    'viewPath' => '@app/src/Presentation/Http/View',
    'container' => require __DIR__ . '/container.php',
    'bootstrap'  => ['log'],
    'language'   => 'ru-RU',
    'sourceLanguage' =>'ru-RU',
    'layout'     => 'smartius',
    'name' => 'SmGuide',
    'aliases'    => [
        //'@bower' => '@vendor/yidas/yii2-bower-asset/bower',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules'    => [],
    'components' => [
        'request'      => [
            'enableCsrfValidation' => true,
            'cookieValidationKey' => $_ENV['COOKIE_VALIDATION_KEY'],
            'baseUrl'             => '',
        ],
        'cache'        => [
            'class' => YII_ENV_DEV ? 'yii\caching\DummyCache' : 'yii\caching\FileCache',
        ],
        'user'         => [
            'identityClass'   => 'app\Infrastructure\User\UserIdentity',
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
        'mailer'       => [
            'class'            => '\yii\symfonymailer\Mailer',
            'transport'        => [
                'scheme' => 'smtps',
                'host'       => $_ENV['MAIL_HOST'],
                'username'   => $_ENV['MAIL_USER'],
                'password'   => $_ENV['MAIL_PASS'],
                'port' => 465,
                'options' => ['ssl' => true],
            ],
            'viewPath'         => '@app/src/Presentation/Mail/View',
            'useFileTransport' => false,
        ],
        'log'          => [
        ],
        'db'           => [
            'class' => 'yii\db\Connection',
            'driverName' => $_ENV['DB_DIVER_NAME'],
            'dsn'        => 'pgsql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'],
            'username'   => $_ENV['DB_USERNAME'],
            'password'   => $_ENV['DB_PASSWORD'],
            'emulatePrepare'=> (bool)$_ENV['DB_EMULATE_PREPARE'],
            'enableSchemaCache'=> YII_ENV_DEV ? false : true,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' =>  $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'database' => 0,
        ],
        'authClientCollection' => [
            'class'   => 'yii\authclient\Collection',
            'clients' => [
                'rsaa' => [
                    'class'        => 'app\Infrastructure\Auth\RsaaAuthClient',
                    'clientId'     => $_ENV['RSAA_CLIENT'],
                    'clientSecret' => $_ENV['RSAA_SECRET'],
                    'authUrl'      => $_ENV['RSAA_AUTH_URL'],
                    'tokenUrl'     => $_ENV['RSAA_TOKEN_URL'],
                    'validateAuthState' => false
                ],
                // и т.д.
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => false,
            'suffix'              => '',
            'rules'               => $url_rules,

        ],
        'i18n'       => [
            'translations' => [
                '*' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'ru_RU',
                    'fileMap'        => [
                        'app/auth'    => 'auth.php',
                    ],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
    ],
    'params'     =>[
        'adminEmail'     => 'smguide@bk.ru',
        'language'       => 'ru-RU',
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
            if($val=='chatbots'){
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


if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'      => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1','*'],
    ];
}
return $config;
