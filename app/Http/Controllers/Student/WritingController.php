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

class WritingController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function index(): Response
    {
        $tenant = session('tenant');

        $types = QuestionType::where('module', 'writing')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($type) => [
                ...$type->toArray(),
                'question_count' => Question::where('tenant_id', $tenant->id)
                    ->where('question_type_id', $type->id)->active()->count(),
            ]);

        return Inertia::render('Student/Writing/Index', [
            'questionTypes' => $types,
        ]);
    }

    public function practice(string $type): Response
    {
        $tenant = session('tenant');
        $questionType = QuestionType::where('slug', $type)->where('module', 'writing')->firstOrFail();

        $questions = Question::with('questionType')
            ->where('tenant_id', $tenant->id)
            ->where('question_type_id', $questionType->id)
            ->active()
            ->get();

        return Inertia::render('Student/Writing/Practice', [
            'questionType' => $questionType,
            'questions'    => $questions,
        ]);
    }

    public function question(Question $question): Response
    {
        $question->load('questionType');

        $attempt = Attempt::create([
            'user_id'     => Auth::id(),
            'question_id' => $question->id,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);

        return Inertia::render('Student/Writing/Question', [
            'question' => $question,
            'attempt'  => $attempt,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attempt_id'  => ['required', 'exists:attempts,id'],
            'text_answer' => ['required', 'string', 'min:1'],
            'time_taken'  => ['nullable', 'integer'],
        ]);

        $attempt = Attempt::where('id', $validated['attempt_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $answer = AttemptAnswer::create([
            'attempt_id'  => $attempt->id,
            'text_answer' => $validated['text_answer'],
        ]);

        $attempt->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
            'time_taken'   => $validated['time_taken'] ?? null,
        ]);

        $this->scoring->scoreWriting($attempt, $answer);
        $attempt->update(['status' => 'scored']);

        return redirect()->route('student.writing.results', $attempt);
    }

    public function results(Attempt $attempt): Response
    {
        $this->authorize('view', $attempt);
        $attempt->load(['question.questionType', 'answer', 'score']);

        return Inertia::render('Student/Writing/Results', [
            'attempt' => $attempt,
        ]);
    }
}
