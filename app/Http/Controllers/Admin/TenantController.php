<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/SuperAdmin/Tenants', [
            'tenants' => Tenant::withCount('users')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email'],
            'admin_name'     => ['required', 'string'],
            'admin_email'    => ['required', 'email'],
            'admin_password' => ['required', 'min:8'],
            'country'        => ['nullable', 'string', 'size:2'],
        ]);

        $tenant = Tenant::create([
            'name'    => $validated['name'],
            'slug'    => Str::slug($validated['name']),
            'email'   => $validated['email'],
            'country' => $validated['country'] ?? 'AU',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name'      => $validated['admin_name'],
            'email'     => $validated['admin_email'],
            'password'  => Hash::make($validated['admin_password']),
            'role'      => 'tenant_admin',
        ]);

        return back()->with('success', "Tenant {$tenant->name} created.");
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update($request->validate([
            'name'      => ['required', 'string'],
            'is_active' => ['boolean'],
        ]));

        return back()->with('success', 'Tenant updated.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return back()->with('success', 'Tenant deleted.');
    }
}
