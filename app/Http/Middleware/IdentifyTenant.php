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
        // Already resolved this request cycle
        if (session()->has('tenant')) {
            return $next($request);
        }

        $host   = $request->getHost();
        $tenant = null;

        // Subdomain: institution.pteportal.com
        $centralDomain = config('app.central_domain', env('CENTRAL_DOMAIN', 'pteportal.com'));
        if (str_ends_with($host, '.' . $centralDomain)) {
            $subdomain = str_replace('.' . $centralDomain, '', $host);
            $tenant    = Tenant::where('slug', $subdomain)->where('is_active', true)->first();
        }

        // Custom domain
        if (! $tenant) {
            $tenant = Tenant::where('domain', $host)->where('is_active', true)->first();
        }

        // Local / Codespaces dev: ?tenant=slug query param
        if (! $tenant) {
            $slug = $request->query('tenant');
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
