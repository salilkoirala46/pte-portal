<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\MockTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user()->load('activeSubscription.plan');

        $recentAttempts = Attempt::with(['question.questionType', 'score'])
            ->where('user_id', $user->id)
            ->where('status', 'scored')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_attempts'    => Attempt::where('user_id', $user->id)->count(),
            'speaking_attempts' => Attempt::where('user_id', $user->id)
                ->whereHas('question.questionType', fn($q) => $q->where('module', 'speaking'))
                ->count(),
            'reading_attempts'  => Attempt::where('user_id', $user->id)
                ->whereHas('question.questionType', fn($q) => $q->where('module', 'reading'))
                ->count(),
            'writing_attempts'  => Attempt::where('user_id', $user->id)
                ->whereHas('question.questionType', fn($q) => $q->where('module', 'writing'))
                ->count(),
            'listening_attempts'=> Attempt::where('user_id', $user->id)
                ->whereHas('question.questionType', fn($q) => $q->where('module', 'listening'))
                ->count(),
            'average_score'     => Attempt::where('user_id', $user->id)
                ->whereHas('score')
                ->with('score')
                ->get()
                ->avg(fn($a) => $a->score?->percentage) ?? 0,
        ];

        return Inertia::render('Student/Dashboard', [
            'user'           => $user,
            'stats'          => $stats,
            'recentAttempts' => $recentAttempts,
        ]);
    }

    public function mockTest(): Response
    {
        $tenant = session('tenant');
        $mockTests = MockTest::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->get();

        return Inertia::render('Student/MockTest', [
            'mockTests' => $mockTests,
        ]);
    }

    public function startMockTest(Request $request)
    {
        $validated = $request->validate([
            'mock_test_id' => ['required', 'exists:mock_tests,id'],
        ]);

        // Create mock test attempt and redirect
        return redirect()->route('student.dashboard')
            ->with('info', 'Mock test started!');
    }
}
