<?php

namespace TestEbanx\Infra\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TestEbanx\Application\UseCases\AccountManager;

class AccountController
{
    public function __construct(
      private readonly AccountManager $accountManager
    ) {
    }


    public function reset(Request $request): Response
    {
        $this->accountManager->resetAccounts();
        return new Response(null, Response::HTTP_OK);
    }
}