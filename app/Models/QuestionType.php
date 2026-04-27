<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    protected $fillable = [
        'module', 'name', 'slug', 'description', 'instructions',
        'prep_time', 'response_time', 'word_limit_min', 'word_limit_max',
        'requires_audio_input', 'requires_audio_response',
        'requires_text_response', 'has_options', 'sort_order',
    ];

    protected $casts = [
        'requires_audio_input'    => 'boolean',
        'requires_audio_response' => 'boolean',
        'requires_text_response'  => 'boolean',
        'has_options'             => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function getModuleColorAttribute(): string
    {
        return match ($this->module) {
            'speaking'  => 'purple',
            'reading'   => 'cyan',
            'writing'   => 'emerald',
            'listening' => 'amber',
            default     => 'gray',
        };
    }
}
