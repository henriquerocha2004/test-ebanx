<?php

use GuzzleHttp\Client;

beforeEach(function () {
   $this->clientHttp = new Client([
       'base_uri' => 'http://localhost:8000'
   ]);
});


test('should found endpoint /reset', function () {
    $response = $this->clientHttp->request('POST', '/reset');
    expect($response->getStatusCode())->toBe(200);
});