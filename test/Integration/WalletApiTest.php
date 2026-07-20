<?php

declare(strict_types=1);

namespace HyperfTest\Integration;

use HyperfTest\HttpTestCase;

class WalletApiTest extends HttpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->client->request('POST', '/reset');
    }

    public function testResetReturns200Ok(): void
    {
        $response = $this->client->request('POST', '/reset');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', (string) $response->getBody());
    }

    public function testGetBalanceForNonExistingAccountReturns404(): void
    {
        $response = $this->client->request('GET', '/balance', [
            'query' => ['account_id' => '1234'],
        ]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('0', (string) $response->getBody());
    }

    public function testDepositCreatesAccountAndReturns201(): void
    {
        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 10],
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame(['destination' => ['id' => '100', 'balance' => 10]], $body);
    }

    public function testDepositIntoExistingAccountReturns201(): void
    {
        $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 10],
        ]);

        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 10],
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame(['destination' => ['id' => '100', 'balance' => 20]], $body);
    }

    public function testGetBalanceForExistingAccountReturns200(): void
    {
        $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 20],
        ]);

        $response = $this->client->request('GET', '/balance', [
            'query' => ['account_id' => '100'],
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('20', (string) $response->getBody());
    }

    public function testWithdrawFromNonExistingAccountReturns404(): void
    {
        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'withdraw', 'origin' => '200', 'amount' => 10],
        ]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('0', (string) $response->getBody());
    }

    public function testWithdrawFromExistingAccountReturns201(): void
    {
        $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 20],
        ]);

        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'withdraw', 'origin' => '100', 'amount' => 5],
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame(['origin' => ['id' => '100', 'balance' => 15]], $body);
    }

    public function testTransferFromNonExistingAccountReturns404(): void
    {
        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'transfer', 'origin' => '200', 'destination' => '300', 'amount' => 15],
        ]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('0', (string) $response->getBody());
    }

    public function testTransferFromExistingAccountReturns201(): void
    {
        $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 15],
        ]);

        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'transfer', 'origin' => '100', 'destination' => '300', 'amount' => 15],
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame([
            'origin' => ['id' => '100', 'balance' => 0],
            'destination' => ['id' => '300', 'balance' => 15],
        ], $body);
    }

    public function testTransferIntoExistingDestinationReturns201(): void
    {
        $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 15],
        ]);
        $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '300', 'amount' => 5],
        ]);

        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'transfer', 'origin' => '100', 'destination' => '300', 'amount' => 10],
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertSame([
            'origin' => ['id' => '100', 'balance' => 5],
            'destination' => ['id' => '300', 'balance' => 15],
        ], $body);
    }

    public function testWithdrawWithInsufficientFundsReturns404(): void
    {
        $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 5],
        ]);

        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'withdraw', 'origin' => '100', 'amount' => 50],
        ]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('0', (string) $response->getBody());
    }

    public function testTransferWithInsufficientFundsReturns404(): void
    {
        $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'deposit', 'destination' => '100', 'amount' => 5],
        ]);

        $response = $this->client->request('POST', '/event', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['type' => 'transfer', 'origin' => '100', 'destination' => '300', 'amount' => 50],
        ]);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('0', (string) $response->getBody());
    }
}
