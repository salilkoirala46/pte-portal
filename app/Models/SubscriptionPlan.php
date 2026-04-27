<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'name', 'slug', 'description', 'price', 'currency',
        'interval', 'trial_days', 'is_active', 'is_featured', 'features',
        'max_attempts_per_day', 'includes_mock_test', 'includes_ai_feedback',
    ];

    protected $casts = [
        'price'                 => 'decimal:2',
        'is_active'             => 'boolean',
        'is_featured'           => 'boolean',
        'features'              => 'array',
        'includes_mock_test'    => 'boolean',
        'includes_ai_feedback'  => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    public function getIntervalLabelAttribute(): string
    {
        return match ($this->interval) {
            'monthly'   => 'per month',
            'quarterly' => 'per 3 months',
            'yearly'    => 'per year',
            default     => $this->interval,
        };
    }
}
