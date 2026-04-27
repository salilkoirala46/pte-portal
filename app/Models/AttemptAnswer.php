<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id', 'text_answer', 'audio_path', 'audio_duration',
        'selected_options', 'arranged_order', 'filled_blanks', 'highlighted_words',
    ];

    protected $casts = [
        'selected_options'  => 'array',
        'arranged_order'    => 'array',
        'filled_blanks'     => 'array',
        'highlighted_words' => 'array',
    ];

    public function attempt()
    {
        return $this->belongsTo(Attempt::class);
    }

    public function getAudioUrlAttribute(): ?string
    {
        return $this->audio_path ? asset('storage/' . $this->audio_path) : null;
    }
}
