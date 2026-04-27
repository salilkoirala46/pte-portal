<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(): Response
    {
        $tenant = session('tenant');
        $user   = Auth::user()->load('activeSubscription.plan');

        $plans = SubscriptionPlan::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        return Inertia::render('Student/Subscription/Plans', [
            'plans'              => $plans,
            'currentSubscription'=> $user->activeSubscription,
        ]);
    }

    public function subscribe(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasActiveSubscription()) {
            return back()->with('error', 'You already have an active subscription.');
        }

        if ($plan->tenant_id !== session('tenant')->id) {
            abort(403);
        }

        $trialDays = $plan->trial_days;
        $now       = now();

        $endsAt = match ($plan->interval) {
            'monthly'   => $now->copy()->addMonth(),
            'quarterly' => $now->copy()->addMonths(3),
            'yearly'    => $now->copy()->addYear(),
        };

        Subscription::create([
            'user_id'       => $user->id,
            'plan_id'       => $plan->id,
            'status'        => $trialDays > 0 ? 'trial' : 'active',
            'trial_ends_at' => $trialDays > 0 ? $now->copy()->addDays($trialDays) : null,
            'starts_at'     => $now,
            'ends_at'       => $endsAt,
        ]);

        return redirect()->route('student.dashboard')
            ->with('success', "You're now subscribed to {$plan->name}!");
    }

    public function cancel(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $sub  = $user->activeSubscription;

        if (! $sub) {
            return back()->with('error', 'No active subscription found.');
        }

        $sub->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'Your subscription has been cancelled. You can still access until the end of your billing period.');
    }
}
