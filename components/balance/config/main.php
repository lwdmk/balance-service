<?php
return [
    'container' => [
        'singleton' => [
            \app\components\balance\services\BalanceService::class => [
                'class' => \app\components\balance\services\BalanceService::class
            ],
            \app\components\balance\services\TransactionsService::class =>[
                'class' => \app\components\balance\services\TransactionsService::class
            ]
        ]
    ]
];