<?php

use DI\Container;
use DI\ContainerBuilder;
use TestEbanx\Application\UseCases\AccountManager;
use TestEbanx\Infra\Controllers\AccountController;
use TestEbanx\Infra\Storage\AccountFileRepository;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    AccountFileRepository::class => function (Container $c) {
        return new AccountFileRepository();
    },
    AccountManager::class => function (Container $c) {
        return new AccountManager(
            $c->get(AccountFileRepository::class)
        );
    },
    AccountController::class => function (Container $c) {
        return new AccountController(
            $c->get(AccountManager::class)
        );
    }
]);
return $containerBuilder->build();