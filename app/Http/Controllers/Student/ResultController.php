<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ResultController extends Controller
{
    public function index(): Response
    {
        $attempts = Attempt::with(['question.questionType', 'score'])
            ->where('user_id', Auth::id())
            ->where('status', 'scored')
            ->latest()
            ->paginate(20);

        return Inertia::render('Student/Results/Index', [
            'attempts' => $attempts,
        ]);
    }

    public function show(Attempt $attempt): Response
    {
        $this->authorize('view', $attempt);

        $attempt->load(['question.questionType', 'question.options', 'answer', 'score']);

        return Inertia::render('Student/Results/Show', [
            'attempt' => [
                ...$attempt->toArray(),
                'answer' => $attempt->answer ? [
                    ...$attempt->answer->toArray(),
                    'audio_url' => $attempt->answer->audio_url,
                ] : null,
                'question' => [
                    ...$attempt->question->toArray(),
                    'audio_url' => $attempt->question->audio_url,
                    'image_url' => $attempt->question->image_url,
                ],
            ],
        ]);
    }
}
