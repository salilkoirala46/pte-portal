<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'question_type_id', 'title', 'content',
        'additional_content', 'image_path', 'audio_path', 'audio_duration',
        'blanks', 'word_bank', 'paragraphs', 'correct_answer',
        'sample_answer', 'scoring_rubric', 'difficulty', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'blanks'         => 'array',
        'word_bank'      => 'array',
        'paragraphs'     => 'array',
        'scoring_rubric' => 'array',
        'is_active'      => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function questionType()
    {
        return $this->belongsTo(QuestionType::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getAudioUrlAttribute(): ?string
    {
        return $this->audio_path ? asset('storage/' . $this->audio_path) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForModule($query, string $module)
    {
        return $query->whereHas('questionType', fn($q) => $q->where('module', $module));
    }

    public function scopeOfType($query, string $slug)
    {
        return $query->whereHas('questionType', fn($q) => $q->where('slug', $slug));
    }
}
