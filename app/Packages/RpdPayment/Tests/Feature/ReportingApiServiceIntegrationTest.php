<?php

use App\Packages\RpdPayment\Services\ReportingApiService;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->service = new ReportingApiService();
});

test('it makes a real API call to get token indirectly', function () {
    // This will be triggered implicitly
    $response = $this->service->transactionReport([
        'fromDate' => now()->subDays(7)->toDateString(),
        'toDate' => now()->toDateString(),
    ]);

    expect($response)->toBeArray();
    expect($response)->toHaveKey('response');
});

test('it makes a real API call to transaction report', function () {
    $response = $this->service->transactionReport([
        'fromDate' => now()->subDays(7)->toDateString(),
        'toDate' => now()->toDateString(),
    ]);

    expect($response)->toBeArray();
    expect($response)->toHaveKey('response');
});

test('it makes a real API call to transaction list with page', function () {
    $response = $this->service->transactionList([
        'status' => 'APPROVED',
        'fromDate' => now()->subDays(7)->toDateString(),
        'toDate' => now()->toDateString(),
    ], 1);

    expect($response)->toBeArray();
    expect($response)->toHaveKey('data');
});

test('it makes a real API call to transaction list without page', function () {
    $response = $this->service->transactionList([
        'status' => 'APPROVED',
        'fromDate' => now()->subDays(7)->toDateString(),
        'toDate' => now()->toDateString(),
    ]);

    expect($response)->toBeArray();
    expect($response)->toHaveKey('data');
});

