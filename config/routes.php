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
    ]
];
