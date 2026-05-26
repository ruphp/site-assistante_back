<?php
// файл  для запуска в консоле в папке проекта через команду: php yii.php
require __DIR__ . '/vendor/autoload.php'; // для загрузки всего необходимого

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';// подкл всю библиотеку yii
$config = require __DIR__ . '/config/console.php';  // конфигурация консольного приложения
(new yii\console\Application($config))->run(); // запуск
