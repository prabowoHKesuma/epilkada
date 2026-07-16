<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });
        
        Event::listen(Login::class, function (Login $event) {
            AuditLogger::log('login_success', 'Login berhasil: '.$event->user->username);
        });

        Event::listen(Failed::class, function (Failed $event) {
            $username = $event->credentials['username'] ?? '-';
            AuditLogger::log('login_failed', "Percobaan login gagal untuk username: $username");
        });
    }
}
