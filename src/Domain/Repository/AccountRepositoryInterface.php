<?php

namespace TestEbanx\Domain\Repository;

use TestEbanx\Domain\Entity\Account;

interface AccountRepositoryInterface
{
    public function save(Account $account): void;
    public function find(int $id): Account|null;
    public function all(): array|null;
    public function update(int $id, Account $account): void;
    public function restart(): void;
}