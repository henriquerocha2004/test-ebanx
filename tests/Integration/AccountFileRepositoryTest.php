<?php

use TestEbanx\Domain\Entity\Account;
use TestEbanx\Infra\Storage\AccountFileRepository;

$accountRepository = new AccountFileRepository();

beforeEach(function () use ($accountRepository) {
   $accountRepository->restart();
});

test('should find account', function () use ($accountRepository) {
    $accountRepository->save(new Account(
        id: 400,
        amount: 200
    ));
    $account = $accountRepository->find(400);
    expect($account->id)->toBe(400);
});

test('should return null if not found account', function () use ($accountRepository) {
    $account = $accountRepository->find(432);
    expect($account)->toBe(null);
});

test('should save account', function () use ($accountRepository) {
    $accountRepository->save(new Account(
        id: 400,
        amount: 200
    ));
    $contentFile = $accountRepository->all();
    expect(count($contentFile['accounts']))->toBe(2);
});