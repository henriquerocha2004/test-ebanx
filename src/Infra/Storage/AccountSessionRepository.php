<?php

namespace TestEbanx\Infra\Storage;

use TestEbanx\Domain\Entity\Account;
use TestEbanx\Domain\Repository\AccountRepositoryInterface;

class AccountSessionRepository implements AccountRepositoryInterface
{
    public function __construct()
    {
        if (PHP_SAPI == 'cli') {
            $_SESSION = [];
        }

//        if (session_status() === PHP_SESSION_NONE) {
//            session_start();
//        }
        if (!isset($_SESSION['accounts'])) {
            $_SESSION['accounts'][] = [
                'id' => 300,
                'amount' => 0.00
            ];
        }
    }

    public function save(Account $account): void
    {
       $_SESSION['accounts'][] = [
           'id' => $account->id,
           'amount' => $account->balance()
       ];
    }

    public function find(int $id): Account|null
    {
        $result = array_values(array_filter($_SESSION['accounts'], fn($account) => $account['id'] == $id));
        if (empty($result)) {
            return null;
        }

        return new Account(
            id: $result[0]['id'],
            amount: $result[0]['amount']
        );
    }

    public function restart(): void
    {
        unset($_SESSION['accounts']);
    }
}