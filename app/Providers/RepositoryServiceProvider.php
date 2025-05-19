<?php

namespace App\Providers;

use App\Interfaces\StatisticsRepositoryInterface;
use App\Interfaces\TestResultsRepositoryInterface;
use App\Repositories\StatisticsRepository;
use App\Repositories\TestResultsRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TestResultsRepositoryInterface::class,TestResultsRepository::class);
        $this->app->bind(StatisticsRepositoryInterface::class,StatisticsRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
