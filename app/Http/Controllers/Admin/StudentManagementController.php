<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentManagementController extends Controller
{
    public function index(Request $request): Response
    {
        $tenant = session('tenant');

        $students = User::where('tenant_id', $tenant->id)
            ->where('role', 'student')
            ->with('activeSubscription.plan')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'filters'  => $request->only('search'),
        ]);
    }

    public function show(User $user): Response
    {
        $user->load(['activeSubscription.plan']);

        $attemptStats = $user->attempts()
            ->selectRaw('COUNT(*) as total')
            ->first();

        return Inertia::render('Admin/Students/Show', [
            'student'      => $user,
            'attemptStats' => $attemptStats,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Student updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student removed.');
    }
}
