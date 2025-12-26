<?php

namespace App\Providers;
use Kreait\Firebase\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
{
    $this->app->singleton('firebase.messaging', function () {
        return (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createMessaging();
    });
}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
