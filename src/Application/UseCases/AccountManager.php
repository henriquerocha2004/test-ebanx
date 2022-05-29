<?php

namespace TestEbanx\Application\UseCases;

use TestEbanx\Domain\Repository\AccountRepositoryInterface;

class AccountManager
{
    public function __construct(
      private AccountRepositoryInterface $accountRepository
    ) {
    }

    public function resetAccounts(): void
    {
       $this->accountRepository->restart();
    }
}