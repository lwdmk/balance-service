<?php
return [
    'container' => [
        'singletons' => [
            \app\components\balance\services\BalanceService::class => [
                'class' => \app\components\balance\services\BalanceService::class
            ],
            \app\components\balance\services\TransactionsService::class =>[
                'class' => \app\components\balance\services\TransactionsService::class
            ]
        ]
    ]
];