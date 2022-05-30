<?php

namespace TestEbanx\Application\UseCases;

use TestEbanx\Domain\Entity\Account;
use TestEbanx\Domain\Exceptions\AccountException;
use TestEbanx\Domain\Exceptions\AccountNotFoundException;
use TestEbanx\Domain\Exceptions\InvalidOperationException;
use TestEbanx\Domain\Repository\AccountRepositoryInterface;

class AccountManager
{
    const ALLOWED_TYPE_TRANSACTIONS = [
        Account::OPERATION_TYPE_DEPOSIT,
        Account::OPERATION_TYPE_TRANSFER,
        Account::OPERATION_TYPE_WITHDRAW
    ];

    public function __construct(
      private readonly AccountRepositoryInterface $accountRepository
    ) {
    }

    public function resetAccounts(): void
    {
       $this->accountRepository->restart();
    }

    public function getBalance(int $accountId): float|null
    {
        $account = $this->accountRepository->find($accountId);
        if (is_null($account)) {
            return null;
        }
        return $account->balance();
    }

    /**
     * @throws InvalidOperationException|AccountNotFoundException
     * @throws AccountException
     */
    public function handleTransaction(array $transaction): array
    {
        if (!in_array($transaction['type'], self::ALLOWED_TYPE_TRANSACTIONS)) {
            throw new InvalidOperationException("invalid operation");
        }

        return match ($transaction['type']) {
            Account::OPERATION_TYPE_DEPOSIT => $this->handleDeposit($transaction),
            Account::OPERATION_TYPE_WITHDRAW => $this->handleWithdraw($transaction),
            default => $this->handleTransfer($transaction),
        };
    }

    private function handleDeposit(array $transaction)
    {
        $account = $this->accountRepository->find($transaction['destination']);

        if (is_null($account)) {
            $account = $this->createAccount($transaction);
        }

        $account->deposit($transaction['amount']);
        $this->accountRepository->update($account->id, $account);

        return [
            'destination' => [
                'id' => $account->id,
                'balance' => $account->balance()
            ],
        ];
    }

    public function createAccount(array $accountData): Account
    {
        $this->accountRepository->save(new Account(
            id: $accountData['destination']
        ));

        return $this->accountRepository->find($accountData['destination']);
    }

    /**
     * @throws AccountNotFoundException|AccountException
     */
    private function handleWithdraw(array $transaction): array
    {
        $account = $this->accountRepository->find($transaction['destination']);
        if (is_null($account)) {
            throw new AccountNotFoundException('account not found');
        }

        $account->withdraw($transaction['amount']);
        $this->accountRepository->update($account->id, $account);

        return [
            'origin' => [
                'id' => $account->id,
                'balance' => $account->balance()
            ]
        ];
    }
}