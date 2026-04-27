<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\QuestionType;
use App\Services\ScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ListeningController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function index(): Response
    {
        $tenant = session('tenant');

        $types = QuestionType::where('module', 'listening')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($type) => [
                ...$type->toArray(),
                'question_count' => Question::where('tenant_id', $tenant->id)
                    ->where('question_type_id', $type->id)->active()->count(),
            ]);

        return Inertia::render('Student/Listening/Index', [
            'questionTypes' => $types,
        ]);
    }

    public function practice(string $type): Response
    {
        $tenant = session('tenant');
        $questionType = QuestionType::where('slug', $type)->where('module', 'listening')->firstOrFail();

        $questions = Question::with(['questionType', 'options'])
            ->where('tenant_id', $tenant->id)
            ->where('question_type_id', $questionType->id)
            ->active()
            ->get()
            ->map(fn($q) => [
                ...$q->toArray(),
                'audio_url' => $q->audio_url,
            ]);

        return Inertia::render('Student/Listening/Practice', [
            'questionType' => $questionType,
            'questions'    => $questions,
        ]);
    }

    public function question(Question $question): Response
    {
        $question->load(['questionType', 'options']);

        $attempt = Attempt::create([
            'user_id'     => Auth::id(),
            'question_id' => $question->id,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);

        return Inertia::render('Student/Listening/Question', [
            'question' => [
                ...$question->toArray(),
                'audio_url' => $question->audio_url,
            ],
            'attempt' => $attempt,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attempt_id'        => ['required', 'exists:attempts,id'],
            'text_answer'       => ['nullable', 'string'],
            'selected_options'  => ['nullable', 'array'],
            'filled_blanks'     => ['nullable', 'array'],
            'highlighted_words' => ['nullable', 'array'],
            'time_taken'        => ['nullable', 'integer'],
        ]);

        $attempt = Attempt::where('id', $validated['attempt_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $answer = AttemptAnswer::create([
            'attempt_id'        => $attempt->id,
            'text_answer'       => $validated['text_answer'] ?? null,
            'selected_options'  => $validated['selected_options'] ?? null,
            'filled_blanks'     => $validated['filled_blanks'] ?? null,
            'highlighted_words' => $validated['highlighted_words'] ?? null,
        ]);

        $attempt->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
            'time_taken'   => $validated['time_taken'] ?? null,
        ]);

        $this->scoring->scoreListening($attempt, $answer);
        $attempt->update(['status' => 'scored']);

        return redirect()->route('student.listening.results', $attempt);
    }

    public function results(Attempt $attempt): Response
    {
        $this->authorize('view', $attempt);
        $attempt->load(['question.questionType', 'question.options', 'answer', 'score']);

        return Inertia::render('Student/Listening/Results', [
            'attempt' => [
                ...$attempt->toArray(),
                'question' => [
                    ...$attempt->question->toArray(),
                    'audio_url' => $attempt->question->audio_url,
                ],
            ],
        ]);
    }
}
