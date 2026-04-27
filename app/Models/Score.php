<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = [
        'attempt_id', 'total_score', 'max_score', 'percentage',
        'content_score', 'form_score', 'grammar_score', 'vocabulary_score',
        'spelling_score', 'fluency_score', 'pronunciation_score',
        'feedback', 'detailed_feedback', 'scoring_method',
    ];

    protected $casts = [
        'total_score'         => 'float',
        'max_score'           => 'float',
        'percentage'          => 'float',
        'content_score'       => 'float',
        'form_score'          => 'float',
        'grammar_score'       => 'float',
        'vocabulary_score'    => 'float',
        'spelling_score'      => 'float',
        'fluency_score'       => 'float',
        'pronunciation_score' => 'float',
        'detailed_feedback'   => 'array',
    ];

    public function attempt()
    {
        return $this->belongsTo(Attempt::class);
    }

    public function getGradeAttribute(): string
    {
        return match (true) {
            $this->percentage >= 79 => 'A',
            $this->percentage >= 65 => 'B',
            $this->percentage >= 50 => 'C',
            $this->percentage >= 36 => 'D',
            default                 => 'E',
        };
    }

    public function getGradeColorAttribute(): string
    {
        return match ($this->grade) {
            'A' => 'green',
            'B' => 'blue',
            'C' => 'yellow',
            'D' => 'orange',
            default => 'red',
        };
    }
}
