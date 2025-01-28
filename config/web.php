<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'language' => 'ru',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '4Q9-iVukhbnHijiat3p-BVWXgUn_R557',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'baseUrl' =>''
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => null,
        ],
        'response' => [
            'class' => yii\web\Response::class,
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'api-course/registration' => 'auth/registration',
                'api-course/authorization' => 'auth/authorization',
                'api-course/logout' => 'auth/logout',

                'api-course/activities/available' => 'activities/available',
                'api-course/activities/<id:\d+>/homework' => 'activities/homework',
                'api-course/activities/<id:\d+>/homework/upload' => 'activities/upload-homework',

                'api-course/activities/create' => 'activities/create',
                'api-course/activities/<id:\d+>/delete' => 'activities/delete',
                'api-course/activities/<id:\d+>/update' => 'activities/update',
                'api-course/activities/select/<id:\d+>' => 'activities/select-course',
                'api-course/activities/search' => 'activities/search',

                'api-course/users' => 'users/index',
                'api-course/users/<id:\d+>/role' => 'users/role-update',

                'api-course/logs' => 'logs/index',
                'api-course/logs/<user_id:\d+>' => 'logs/index',
                'api-course/logs/<user_id:\d+>/action/<action:\w+>' => 'logs/index',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
