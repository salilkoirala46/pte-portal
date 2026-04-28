<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin', fn($user) =>
            in_array($user->role, ['tenant_admin', 'super_admin'])
        );

        Gate::define('super-admin', fn($user) =>
            $user->role === 'super_admin'
        );
    }
}
