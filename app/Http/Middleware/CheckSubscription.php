<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Tenant admins and super admins bypass subscription checks
        if ($user && ! $user->isStudent()) {
            return $next($request);
        }

        if (! $user || ! $user->hasActiveSubscription()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Subscription required.'], 402);
            }

            return redirect()->route('student.subscription.index')
                ->with('warning', 'Please subscribe to access practice questions.');
        }

        return $next($request);
    }
}
