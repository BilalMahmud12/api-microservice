<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\CustomerRepositoryInterface;
use App\Repositories\CustomerRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
