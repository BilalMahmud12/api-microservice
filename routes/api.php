<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\AuditController;


Route::group(['prefix' => 'v1'], function () {

    // Customer Routes group
    Route::controller(CustomerController::class)->group(function () {
        Route::get('customers', 'getAllCustomers');
        Route::post('customers', 'createCustomer');
        Route::get('customers/{id}', 'getCustomerById');
        Route::put('customers/{id}', 'updateCustomer');
        Route::delete('customers/{id}', 'deleteCustomer');
    });

    // Accounts Routes group
    Route::controller(AccountController::class)->group(function () {
        Route::get('accounts/{id}', 'getBalance');
        Route::post('accounts/{id}/deposit', 'deposit');
        Route::post('accounts/{id}/withdraw', 'withdraw');
        Route::post('accounts/transfer', 'transfer');
    });

    // Audit Routes group
    Route::controller(AuditController::class)->group(function () {
        Route::post('/audit/all', 'auditAllAccounts');
        Route::post('/audit/{id}', 'auditSingleAccount');
    });

});
