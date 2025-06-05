<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Packages\RpdPayment\Services\ReportingApiService;

uses(Tests\TestCase::class);

beforeEach(function () {
    Http::preventStrayRequests();
    Cache::flush();
});

it('sends transaction report request', function () {
    Http::fake([
        '*/transactions/report' => Http::response([
            'status' => 'APPROVED',
            'response' => [
                ['count' => 1, 'total' => 100, 'currency' => 'USD']
            ]
        ], 200),
    ]);

    Cache::put('reporting_api_token', 'abc123');
    $service = new ReportingApiService();
    $response = $service->transactionReport(['fromDate' => '2024-01-01', 'toDate' => '2024-01-31']);

    expect($response)->toMatchArray([
        'status' => 'APPROVED',
        'response' => [['count' => 1, 'total' => 100, 'currency' => 'USD']]
    ]);
});

it('sends transaction list request with page', function () {
    Http::fake([
        '*/transaction/list?page=2' => Http::response(['data' => [], 'current_page' => 2], 200),
    ]);

    Cache::put('reporting_api_token', 'abc123');
    $service = new ReportingApiService();
    $response = $service->transactionList(['status' => 'APPROVED'], 2);

    expect($response)->toMatchArray(['data' => [], 'current_page' => 2]);
});

it('sends transaction list request without page', function () {
    Http::fake([
        '*/transaction/list' => Http::response(['data' => [], 'current_page' => 1], 200),
    ]);

    Cache::put('reporting_api_token', 'abc123');
    $service = new ReportingApiService();
    $response = $service->transactionList(['status' => 'APPROVED']);

    expect($response)->toMatchArray(['data' => [], 'current_page' => 1]);
});

it('gets transaction details', function () {
    Http::fake([
        '*/transaction' => Http::response(['data' => ['id' => 'txn_123']], 200),
    ]);

    Cache::put('reporting_api_token', 'abc123');
    $service = new ReportingApiService();
    $response = $service->getTransaction('txn_123');

    expect($response)->toMatchArray(['data' => ['id' => 'txn_123']]);
});

it('gets client details', function () {
    Http::fake([
        '*/client' => Http::response(['data' => ['id' => 'client_456']], 200),
    ]);

    Cache::put('reporting_api_token', 'abc123');
    $service = new ReportingApiService();
    $response = $service->getClient('txn_123');

    expect($response)->toMatchArray(['data' => ['id' => 'client_456']]);
});
