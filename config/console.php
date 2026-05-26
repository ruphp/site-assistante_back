<?php


$config = [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components'          => [
        'cache' => [
            'class' => YII_ENV_DEV ? 'yii\caching\DummyCache' : 'yii\caching\FileCache',
        ],
        'log'   => [
        ],
        'db'           => [
            'class' => 'yii\db\Connection',
            'driverName' => $_ENV['DB_DIVER_NAME'],
            'dsn' => 'pgsql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'] ,
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset' => 'utf8',
            'enableSchemaCache'=> YII_ENV_DEV ? false : true,
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' =>  $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'database' => 0,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager'
        ],
    ],
    'params'              => [
        'adminEmail'     => 'smguide@bk.ru',
        'language'       => 'ru-RU',
        'sourceLanguage' => 'ru-RU',
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@app/migrations',
                '@yii/rbac/migrations',
            ],
        ],
    ],



];
// получаем список директорий в protected/modules
$dirs = scandir(dirname(__FILE__) . '/../modules');

foreach ($dirs as $val) {
    if ($val[0] != '.') {
        $config['bootstrap'][] = $val;
        $config['modules'][$val] = ['class' => 'app\modules\\' . $val . '\Module'];
        $config['controllerMap']['migrate']['migrationPath'][] = '@app/modules/' . $val . '/migrations';
        if($val=='chatbots'){
            // получаем список директорий в подмодулях чатбота
            $child_dirs = scandir(dirname(__FILE__) . '/../modules/chatbots/modules');
            foreach ($child_dirs as $child_val) {
                if ($child_val[0] != '.') {
                    $config['bootstrap'][] = $child_val;
                    $config['modules'][$child_val] = ['class' => 'app\modules\chatbots\modules\\' . $child_val . '\Module'];
                    $config['controllerMap']['migrate']['migrationPath'][] = '@app/modules/chatbots/modules/' . $child_val . '/migrations';
                }
            }
        }
    }
}

return $config;
