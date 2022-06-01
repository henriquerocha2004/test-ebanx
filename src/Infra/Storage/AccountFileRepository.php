<?php

namespace TestEbanx\Infra\Storage;

use TestEbanx\Domain\Entity\Account;
use TestEbanx\Domain\Repository\AccountRepositoryInterface;

class AccountFileRepository implements AccountRepositoryInterface
{
    public function __construct()
    {
        if (PHP_SAPI == 'cli') {
            $_SESSION = [];
        } else {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }

        if (!isset($_SESSION['accounts'])) {
            $_SESSION['accounts'][] = [
                'id' => 300,
                'amount' => 0.00
            ];
        }
    }

    public function save(Account $account): void
    {
       $contentFile = $this->getContentFile();
       $contentFile['accounts'][] = [
           'id' => $account->id,
           'amount' => $account->balance()
       ];
        $this->putContentFile($contentFile);
    }

    public function find(int $id): Account|null
    {
        $contentFile = $this->getContentFile();
        $result = array_values(array_filter($contentFile['accounts'], fn($account) => $account['id'] == $id));
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
        $file = fopen(__DIR__. '/accounts.json', 'w');
        fwrite($file, json_encode([
            'accounts' => [
                [
                    'id' => 300,
                    'amount' => 0.00
                ]
            ]
        ]));
        fclose($file);
    }

    public function update(int $id, Account $account): void
    {
        $contentFile = $this->getContentFile();
        foreach ($contentFile['accounts'] as $key => $accountFile) {
            if ($accountFile['id'] == $id) {
                $contentFile['accounts'][$key] = [
                   'id' => $account->id,
                   'amount' => $account->balance()
               ];
            }
        }
        $this->putContentFile($contentFile);
    }

    public function all(): array|null
    {
        return $this->getContentFile();
    }

    private function getContentFile(): array
    {
        return json_decode(file_get_contents(__DIR__. '/accounts.json'), true);
    }

    private function putContentFile(array $content): void
    {
        $file = fopen(__DIR__. '/accounts.json', 'w');
        fwrite($file, json_encode($content));
        fclose($file);
    }
}