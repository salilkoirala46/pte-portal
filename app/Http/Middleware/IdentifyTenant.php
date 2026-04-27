<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        // Already set in session
        if (session()->has('tenant')) {
            return $next($request);
        }

        $host   = $request->getHost();
        $tenant = null;

        // Subdomain detection: institution1.pteportal.com
        $centralDomain = config('app.central_domain', 'pteportal.com');
        if (str_ends_with($host, '.' . $centralDomain)) {
            $subdomain = str_replace('.' . $centralDomain, '', $host);
            $tenant    = Tenant::where('slug', $subdomain)->where('is_active', true)->first();
        }

        // Custom domain detection
        if (! $tenant) {
            $tenant = Tenant::where('domain', $host)->where('is_active', true)->first();
        }

        // For local dev: use query param ?tenant=slug
        if (! $tenant && app()->isLocal()) {
            $slug = $request->query('tenant') ?? config('app.default_tenant');
            if ($slug) {
                $tenant = Tenant::where('slug', $slug)->where('is_active', true)->first();
            }
        }

        if ($tenant) {
            session(['tenant' => $tenant]);
        }

        return $next($request);
    }
}
