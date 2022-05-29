<?php

use DI\Container;
use DI\ContainerBuilder;
use TestEbanx\Application\UseCases\AccountManager;
use TestEbanx\Infra\Controllers\AccountController;
use TestEbanx\Infra\Storage\AccountSessionRepository;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    AccountSessionRepository::class => function (Container $c) {
        return new AccountSessionRepository();
    },
    AccountManager::class => function (Container $c) {
        return new AccountManager(
            $c->get(AccountSessionRepository::class)
        );
    },
    AccountController::class => function (Container $c) {
        return new AccountController(
            $c->get(AccountManager::class)
        );
    }
]);
return $containerBuilder->build();