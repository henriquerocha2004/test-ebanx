<?php

namespace TestEbanx\Domain\Repository;

use TestEbanx\Domain\Entity\Account;

interface AccountRepositoryInterface
{
    public function save(Account $account): void;
    public function find(int $id): Account|null;
    public function restart(): void;
}