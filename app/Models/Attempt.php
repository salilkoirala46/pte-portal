<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'question_id', 'status',
        'time_taken', 'started_at', 'submitted_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->hasOne(AttemptAnswer::class);
    }

    public function score()
    {
        return $this->hasOne(Score::class);
    }

    public function isScored(): bool
    {
        return $this->status === 'scored';
    }
}
