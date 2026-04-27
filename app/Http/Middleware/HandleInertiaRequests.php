<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user   = $request->user();
        $tenant = session('tenant');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id'                  => $user->id,
                    'name'                => $user->name,
                    'email'               => $user->email,
                    'role'                => $user->role,
                    'avatar_url'          => $user->avatar_url,
                    'has_subscription'    => $user->hasActiveSubscription(),
                    'subscription'        => $user->activeSubscription?->load('plan'),
                ] : null,
            ],
            'tenant' => $tenant ? [
                'id'            => $tenant->id,
                'name'          => $tenant->name,
                'slug'          => $tenant->slug,
                'logo_url'      => $tenant->logo_url,
                'primary_color' => $tenant->primary_color,
            ] : null,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info'    => fn () => $request->session()->get('info'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
