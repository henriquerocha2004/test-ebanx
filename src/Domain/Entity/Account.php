<?php

namespace TestEbanx\Domain\Entity;

use TestEbanx\Domain\Exceptions\AccountException;

class Account
{
    public function __construct(
        public readonly int $id,
        private float $amount = 0.00
    ) {
    }

    public function deposit(float $amount): void
    {
        $this->amount += $amount;
    }

    public function withdraw(float $amount): void
    {
        if ($this->amount < $amount) {
            throw new AccountException("cannot withdraw. this account does not have enough balance");
        }

        $this->amount -= $amount;
    }

    public function balance(): float
    {
        return $this->amount;
    }

    /**
     * @throws AccountException
     */
    public function transfer(float $amount, Account $destinationAccount): void
    {
        $this->withdraw($amount);
        $destinationAccount->deposit($amount);
    }
}