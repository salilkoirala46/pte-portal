<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $tenant = session('tenant');

        $stats = [
            'total_students'      => User::where('tenant_id', $tenant->id)->where('role', 'student')->count(),
            'active_subscriptions'=> Subscription::whereHas('user', fn($q) => $q->where('tenant_id', $tenant->id))
                ->whereIn('status', ['trial', 'active'])->count(),
            'total_questions'     => Question::where('tenant_id', $tenant->id)->count(),
            'total_attempts'      => Attempt::whereHas('user', fn($q) => $q->where('tenant_id', $tenant->id))->count(),
        ];

        $recentStudents = User::where('tenant_id', $tenant->id)
            ->where('role', 'student')
            ->with('activeSubscription.plan')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'tenant'         => $tenant,
            'stats'          => $stats,
            'recentStudents' => $recentStudents,
        ]);
    }

    public function reports(): Response
    {
        $tenant = session('tenant');

        $moduleStats = collect(['speaking', 'reading', 'writing', 'listening'])->mapWithKeys(function ($module) use ($tenant) {
            $attempts = Attempt::whereHas('user', fn($q) => $q->where('tenant_id', $tenant->id))
                ->whereHas('question.questionType', fn($q) => $q->where('module', $module))
                ->with('score')
                ->get();

            return [$module => [
                'total_attempts' => $attempts->count(),
                'avg_score'      => $attempts->avg(fn($a) => $a->score?->percentage) ?? 0,
            ]];
        });

        return Inertia::render('Admin/Reports', [
            'moduleStats' => $moduleStats,
        ]);
    }

    public function settings(): Response
    {
        return Inertia::render('Admin/Settings', [
            'tenant' => session('tenant'),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $tenant = session('tenant');
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email'],
            'phone'         => ['nullable', 'string'],
            'address'       => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'timezone'      => ['nullable', 'string'],
        ]);

        $tenant->update($validated);

        return back()->with('success', 'Settings updated successfully.');
    }
}
