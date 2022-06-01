<?php

namespace TestEbanx\Infra\Controllers;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TestEbanx\Application\UseCases\AccountManager;
use TestEbanx\Domain\Exceptions\AccountNotFoundException;
use TestEbanx\Domain\Exceptions\InvalidOperationException;

class AccountController
{
    public function __construct(
      private readonly AccountManager $accountManager
    ) {
    }

    public function reset(Request $request): Response
    {
        $this->accountManager->resetAccounts();
        return new Response("OK", Response::HTTP_OK);
    }

    public function balance(Request $request): Response
    {
        $accountId = $request->get('account_id');

        if (!filter_var($accountId, FILTER_VALIDATE_INT)) {
            return new Response("invalid account", Response::HTTP_BAD_REQUEST);
        }

        $balance = $this->accountManager->getBalance($accountId);

        if (is_null($balance)) {
            return new Response(0, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($balance, Response::HTTP_OK);
    }

    public function transaction(Request $request): Response
    {
        $bodyContent = json_decode($request->getContent());

        if (
            !filter_var($bodyContent->amount, FILTER_VALIDATE_FLOAT)
        ) {
            return new Response("invalid transaction data", Response::HTTP_BAD_REQUEST);
        }


        if (isset($bodyContent->destination) && !filter_var($bodyContent->destination, FILTER_VALIDATE_INT)) {
            return new Response("invalid transaction data", Response::HTTP_BAD_REQUEST);
        }

        try {
            $transactionData = [
                'type' => filter_var($bodyContent->type, FILTER_DEFAULT),
                'destination' => filter_var($bodyContent->destination, FILTER_SANITIZE_NUMBER_INT),
                'amount' => filter_var($bodyContent->amount, FILTER_SANITIZE_NUMBER_FLOAT),
                'origin' => filter_var($bodyContent->origin ?? "", FILTER_SANITIZE_NUMBER_INT)
            ];

            $result = $this->accountManager->handleTransaction($transactionData);

        } catch (AccountNotFoundException $e) {
            return new Response(0, Response::HTTP_NOT_FOUND);
        } catch (InvalidOperationException $e) {
            return new Response("invalid transaction operation", Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return new Response("failed to process transaction", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse($result, Response::HTTP_CREATED);
    }
}