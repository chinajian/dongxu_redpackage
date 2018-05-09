<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

//var_dump(__DIR__);
//die();
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../../components/fn.php';//引入全局函数文件

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
//    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php'
//    require __DIR__ . '/../config/main-local.php'
);

require_once(__DIR__ . '/../config/constant.php');//引入常量

(new yii\web\Application($config))->run();
