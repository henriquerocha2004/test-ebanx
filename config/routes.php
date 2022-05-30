<?php

use TestEbanx\Infra\Controllers\AccountController;

return [
    [
        'uri' => '/reset',
        'resource' => [
            'controller' => AccountController::class,
            'action' => 'reset'
        ],
        'methods' => ['POST']
    ],
    [
        'uri' => '/balance',
        'resource' => [
            'controller' => AccountController::class,
            'action' => 'balance'
        ],
        'methods' => ['GET']
    ],
    [
        'uri' => '/event',
        'resource' => [
            'controller' => AccountController::class,
            'action' => 'transaction'
        ],
        'methods' => ['POST']
    ]
];
