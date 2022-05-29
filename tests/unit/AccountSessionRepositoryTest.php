<?php

use TestEbanx\Domain\Entity\Account;
use TestEbanx\Infra\Storage\AccountSessionRepository;

beforeEach(function () {
   unset($_SESSION['accounts']);
});

test('should find account', function () {
    $accountRepository = new AccountSessionRepository();
    $accountRepository->save(new Account(
        id: 400,
        amount: 200
    ));
    $account = $accountRepository->find(400);
    expect($account->id)->toBe(400);
});

test('should return null if not found account', function () {
    $accountRepository = new AccountSessionRepository();
    $account = $accountRepository->find(432);
    expect($account)->toBe(null);
});

test('should save account', function () {
    $accountRepository = new AccountSessionRepository();
    $accountRepository->save(new Account(
        id: 400,
        amount: 200
    ));
    expect(count($_SESSION['accounts']))->toBe(2);
});