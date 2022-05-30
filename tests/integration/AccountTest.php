<?php

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SessionCookieJar;

beforeEach(function () {
    $cookie = new SessionCookieJar('PHPSESSID', true);
    $this->clientHttp = new Client([
       'base_uri' => 'http://localhost:8000',
       'cookies' => $cookie
    ]);
});


test('should found endpoint /reset', function () {
    $response = $this->clientHttp->request('POST', '/reset');
    expect($response->getStatusCode())->toBe(200);
});

test('should return 404 when get balance for non-existing account', function () {
    try {
        $this->clientHttp->request('GET', '/balance?account_id=1234');
    } catch (Exception $e) {
       expect($e->getCode())->toBe(404);
    }
});

test('should create account with initial balance', function () {
    $payload = json_encode([
        'type' => 'deposit',
        'destination' => 100,
        'amount' => 10
    ]);

    $response = $this->clientHttp->request('POST', '/event', [
        'body' => $payload
    ]);
    $responseData = json_decode($response->getBody()->getContents(), true);
    expect($response->getStatusCode())->toBe(201);
    expect($responseData)->toBe([
        'destination' => [
            'id' => 100,
            'balance' => 10
        ]
    ]);
});

test('should deposit into existing account', function () {
    $payload = json_encode([
        'type' => 'deposit',
        'destination' => 100,
        'amount' => 10
    ]);

    $this->clientHttp->request('POST', '/event', [
        'body' => $payload
    ]);

    $response = $this->clientHttp->request('POST', '/event', [
        'body' => json_encode([
            'type' => 'deposit',
            'destination' => 100,
            'amount' => 10,
            'test' => 'test'
        ])
    ]);

    $responseData = json_decode($response->getBody()->getContents(), true);
    expect($response->getStatusCode())->toBe(201);
    expect($responseData)->toBe([
        'destination' => [
            'id' => 100,
            'balance' => 20
        ]
    ]);
});

test('should get balance for existing account', function () {
    $payload = json_encode([
        'type' => 'deposit',
        'destination' => 100,
        'amount' => 10
    ]);

    $this->clientHttp->request('POST', '/event', [
        'body' => $payload
    ]);

    $response = $this->clientHttp->request('GET', '/balance?account_id=100');
    $responseData = json_decode($response->getBody()->getContents(), true);
    expect($response->getStatusCode())->toBe(200);
    expect($responseData)->toBe(10);
});

test('should withdraw from non existing account', function () {
    $payload = json_encode([
        'type' => 'withdraw',
        'destination' => 600,
        'amount' => 10
    ]);

    try {
        $this->clientHttp->request('POST', '/event', [
            'body' => $payload
        ]);
    } catch (Exception $e) {
       expect($e->getCode())->toBe(404);
    }
});