<?php

namespace App\Providers;

use App\Contracts\PropertyRepository;
use App\Repositories\EloquentPropertyRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PropertyRepository::class, EloquentPropertyRepository::class);
    }

    public function boot(): void {}
}
