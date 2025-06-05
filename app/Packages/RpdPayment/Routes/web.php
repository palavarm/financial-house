<?php

use App\Packages\RpdPayment\Views\Admin\ClientDetails;
use App\Packages\RpdPayment\Views\Admin\TransactionDetails;
use App\Packages\RpdPayment\Views\Admin\TransactionsReport;
use App\Packages\RpdPayment\Views\Admin\TransactionsList;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/', TransactionsReport::class)->name('rpdpayment.transactions.report');
    Route::get('/transactions', TransactionsList::class)->name('rpdpayment.transactions.list');
    Route::get('/transaction/{transactionId}', TransactionDetails::class)->name('rpdpayment.transactions.details');
    Route::get('/client/{transactionId}', ClientDetails::class)->name('rpdpayment.client.details');
});
