<?php

namespace LaraCare\AccountLockout;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use LaraCare\AccountLockout\Listeners\HandleFailedLogin;

class AccountLockoutServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/account-lockout.php' => config_path('account-lockout.php'),
            ], 'lara-care-lockout-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'lara-care-lockout-migrations');
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Automatic authentication event tracking
        Event::listen(Failed::class, HandleFailedLogin::class);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/account-lockout.php', 'account-lockout');

        $this->app->singleton(Services\LockoutManager::class, function ($app) {
            return new Services\LockoutManager(config('account-lockout'));
        });
    }
}