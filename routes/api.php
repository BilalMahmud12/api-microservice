<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerController;


Route::group(['prefix' => 'v1'], function () {

    Route::controller(CustomerController::class)->group(function () {
        Route::get('customers', 'getAllCustomers');
        Route::post('customers', 'createCustomer');
        Route::get('customers/{id}', 'getCustomerById');
        Route::put('customers/{id}', 'updateCustomer');
        Route::delete('customers/{id}', 'deleteCustomer');
    });

});
