<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'domain', 'logo', 'email', 'phone',
        'address', 'country', 'timezone', 'primary_color',
        'is_active', 'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings'  => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptionPlans()
    {
        return $this->hasMany(SubscriptionPlan::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'tenant_admin');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
