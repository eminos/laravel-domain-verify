<?php

namespace SunAsterisk\DomainVerifier;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use SunAsterisk\DomainVerifier\Factories\VerifierFactory;
use SunAsterisk\DomainVerifier\Repositories\DomainVerification;

class DomainVerifierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'migrations');

            $this->publishes([
                __DIR__.'/../config/domain_verifier.php' => config_path('domain_verifier.php'),
            ], 'config');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-domain-verify');
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DomainVerification::class, function (Application $app) {
            $connection = $app->make('db')->connection();
            $table = 'domain_verifications';
            $hasher = $app->make('hash');
            $hashKey = $app->make('config')->get('app.key');

            return new DomainVerification($connection, $table, $hasher, $hashKey);
        });

        $this->app->singleton(VerifierFactory::class, function () {
            return new VerifierFactory();
        });
    }
}
