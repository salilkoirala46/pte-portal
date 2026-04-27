<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'plan_id', 'status', 'trial_ends_at',
        'starts_at', 'ends_at', 'cancelled_at',
        'payment_reference', 'payment_method', 'metadata',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'cancelled_at'  => 'datetime',
        'metadata'      => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['trial', 'active'])
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->ends_at !== null && $this->ends_at->isPast());
    }

    public function daysRemaining(): int
    {
        if ($this->ends_at === null) return 999;
        return max(0, (int) now()->diffInDays($this->ends_at, false));
    }
}
