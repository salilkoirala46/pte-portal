<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'phone', 'country',
        'password', 'role', 'avatar', 'date_of_birth',
        'target_score', 'exam_date', 'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth'     => 'date',
        'exam_date'         => 'date',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', ['trial', 'active'])
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest();
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isTenantAdmin(): bool
    {
        return $this->role === 'tenant_admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF";
    }
}
