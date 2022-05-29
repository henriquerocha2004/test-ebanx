<?php

use TestEbanx\Domain\Entity\Account;
use TestEbanx\Domain\Exceptions\AccountException;

test("should create a account", function () {
   $account = new Account(
       id: 1,
       amount: 10.00
   );
   expect($account->balance())->toBe(10.00);
});

test('should deposit into an account', function () {
    $account = new Account(
        id: 1,
        amount: 10.00
    );
    $account->deposit(50.00);
    expect($account->balance())->toBe(60.00);
});

test('should withdraw into an account', function () {
    $account = new Account(
        id: 1,
        amount: 10.00
    );
    $account->withdraw(6.00);
    expect($account->balance())->toBe(4.00);
});

test('should transfer amount into an account', function () {
    $account01 = new Account(
        id: 1,
        amount: 10.00
    );

    $account02 = new Account(
        id: 2,
        amount: 10.00
    );
    $account01->transfer(5.00, $account02);
    expect($account01->balance())->toBe(5.00);
    expect($account02->balance())->toBe(15.00);
});

test('should return an error when there is an attempt to withdraw without sufficient account balance', function () {
    $this->expectException(AccountException::class);
    $this->expectErrorMessage("cannot withdraw. this account does not have enough balance");
    $account = new Account(
        id: 1,
        amount: 10.00
    );
    $account->withdraw(20.00);
});

test('should return an error when there is an attempt to transfer without sufficient balance in the account', function () {
    $this->expectException(AccountException::class);
    $this->expectErrorMessage("cannot withdraw. this account does not have enough balance");
    $account01 = new Account(
        id: 1,
        amount: 10.00
    );
    $account02 = new Account(
        id: 2,
        amount: 10.00
    );
    $account01->transfer(20.00, $account02);
});