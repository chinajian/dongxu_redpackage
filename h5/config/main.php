<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);
$db = require __DIR__ . '/db.php';

return [
    'id' => 'app-h5',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'h5\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\admin',//抽奖后台管理模块
        ],
        'api' => [
            'class' => 'app\modules\api\api',//抽奖API
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-h5',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-h5', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the h5
            'name' => 'advanced-h5',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'db' => $db,
        'timeZone' => 'Asia/Shanghai',
        'language' => 'zh-CN',
        'charset' => 'UTF-8',
    ],
    'params' => $params,
];
