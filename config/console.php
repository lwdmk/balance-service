<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$components = \yii\helpers\ArrayHelper::merge([],
    require(__DIR__ . '/../components/balance/config/main.php')
);

$config = [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log', 'externalQueue', 'internalQueue'],
    'controllerNamespace' => 'app\commands',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components'          => [
        'cache'         => [
            'class' => 'yii\caching\FileCache',
        ],
        'redis'         => [
            'class'   => \yii\redis\Connection::class,
            'retries' => 1,
        ],
        'externalQueue' => [
            'class'     => \yii\queue\amqp\Queue::class,
            'host'      => 'localhost',
            'port'      => 5672,
            'user'      => 'rabbitmq',
            'password'  => 'rabbitmq',
            'queueName' => 'balanceOperation',
            'as log'    => \yii\queue\LogBehavior::class,
        ],
        'successQueue'  => [
            'class'     => \yii\queue\amqp\Queue::class,
            'host'      => 'localhost',
            'port'      => 5672,
            'user'      => 'rabbitmq',
            'password'  => 'rabbitmq',
            'queueName' => 'balanceCompletedOperation',
            'as log'    => \yii\queue\LogBehavior::class,
        ],
        'internalQueue' => [
            'class'   => \yii\queue\redis\Queue::class,
            'redis'   => 'redis',
            'channel' => 'transactions',
            'as log'  => \yii\queue\LogBehavior::class,
        ],
        'log'           => [
            'targets' => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'db' => $db,
    ],
    'params'              => $params,
];

return \yii\helpers\ArrayHelper::merge($components, $config);
