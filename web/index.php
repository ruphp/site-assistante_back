<?php
ini_set('max_execution_time', 300);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods','*');

require __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

defined('YII_DEBUG') or define('YII_DEBUG', (bool)$_ENV['YII_DEBUG']);
defined('YII_ENV') or define('YII_ENV',$_ENV['YII_ENV']);

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
$config = require __DIR__ . '/../config/web.php';

//require __DIR__ . '/../function.php';// библиотека функций

//echo $today = date("Y-m-d", strtotime("-40 hours")); exit;






(new yii\web\Application($config))->run();
