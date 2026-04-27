<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $tenant = session('tenant');
        return Inertia::render('Admin/Plans/Index', [
            'plans' => SubscriptionPlan::where('tenant_id', $tenant->id)
                ->withCount('subscriptions')
                ->orderBy('price')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $tenant    = session('tenant');
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'description'           => ['nullable', 'string'],
            'price'                 => ['required', 'numeric', 'min:0'],
            'currency'              => ['required', 'string', 'size:3'],
            'interval'              => ['required', 'in:monthly,quarterly,yearly'],
            'trial_days'            => ['integer', 'min:0'],
            'is_featured'           => ['boolean'],
            'features'              => ['nullable', 'array'],
            'max_attempts_per_day'  => ['integer', 'min:1'],
            'includes_mock_test'    => ['boolean'],
            'includes_ai_feedback'  => ['boolean'],
        ]);

        SubscriptionPlan::create([
            ...$validated,
            'tenant_id' => $tenant->id,
            'slug'      => Str::slug($validated['name']),
        ]);

        return back()->with('success', 'Plan created.');
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'is_active'   => ['boolean'],
            'is_featured' => ['boolean'],
            'features'    => ['nullable', 'array'],
        ]);

        $plan->update($validated);

        return back()->with('success', 'Plan updated.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->update(['is_active' => false]);
        return back()->with('success', 'Plan deactivated.');
    }
}
