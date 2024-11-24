<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\CustomerRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\AccountRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
