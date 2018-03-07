<?php

return [
    'class'               => 'yii\db\Connection',
    'dsn'                 => 'pgsql:host=localhost;dbname=balanceservice',
    'username'            => 'postgres',
    'password'            => 'postgres',
    'charset'             => 'utf8',
    'enableSchemaCache'   => true,
    'schemaCacheDuration' => 60,
    'schemaCache'         => 'cache',
];
