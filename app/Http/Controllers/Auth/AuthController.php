<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showLanding(): Response
    {
        $tenant = session('tenant');
        return Inertia::render('Auth/Landing', [
            'tenant' => $tenant,
        ]);
    }

    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login', [
            'tenant' => session('tenant'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $tenant = session('tenant');

        $user = User::where('email', $credentials['email'])
            ->when($tenant, fn($q) => $q->where('tenant_id', $tenant->id))
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'Your account has been deactivated. Please contact your institution.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectAfterLogin($user);
    }

    public function showRegister(): Response
    {
        $tenant = session('tenant');

        abort_unless($tenant !== null, 404, 'Please access the registration page through your institution\'s URL.');

        return Inertia::render('Auth/Register', [
            'tenant' => $tenant,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $tenant = session('tenant');
        abort_unless($tenant !== null, 403);

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'country'               => ['nullable', 'string', 'max:2'],
            'date_of_birth'         => ['nullable', 'date', 'before:today'],
            'target_score'          => ['nullable', 'string'],
            'exam_date'             => ['nullable', 'date', 'after:today'],
        ]);

        $emailExists = User::where('tenant_id', $tenant->id)
            ->where('email', $validated['email'])
            ->exists();

        if ($emailExists) {
            return back()->withErrors(['email' => 'This email is already registered.']);
        }

        $user = User::create([
            ...$validated,
            'tenant_id' => $tenant->id,
            'role'      => 'student',
            'password'  => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.subscription.index')
            ->with('success', 'Welcome! Please choose a subscription plan to get started.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectAfterLogin(User $user): RedirectResponse
    {
        return match ($user->role) {
            'super_admin'  => redirect()->route('super-admin.tenants.index'),
            'tenant_admin' => redirect()->route('admin.dashboard'),
            default        => redirect()->route('student.dashboard'),
        };
    }
}
