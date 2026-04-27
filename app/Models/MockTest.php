<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockTest extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'name', 'description', 'is_active', 'duration_minutes'];

    protected $casts = ['is_active' => 'boolean'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'mock_test_questions')->withPivot('sort_order')->orderBy('sort_order');
    }
}
