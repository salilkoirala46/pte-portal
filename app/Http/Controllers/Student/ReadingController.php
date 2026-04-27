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

class ReadingController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function index(): Response
    {
        $tenant = session('tenant');

        $types = QuestionType::where('module', 'reading')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($type) => [
                ...$type->toArray(),
                'question_count' => Question::where('tenant_id', $tenant->id)
                    ->where('question_type_id', $type->id)->active()->count(),
            ]);

        return Inertia::render('Student/Reading/Index', [
            'questionTypes' => $types,
        ]);
    }

    public function practice(string $type): Response
    {
        $tenant = session('tenant');
        $questionType = QuestionType::where('slug', $type)->where('module', 'reading')->firstOrFail();

        $questions = Question::with(['questionType', 'options'])
            ->where('tenant_id', $tenant->id)
            ->where('question_type_id', $questionType->id)
            ->active()
            ->get();

        return Inertia::render('Student/Reading/Practice', [
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

        return Inertia::render('Student/Reading/Question', [
            'question' => $question,
            'attempt'  => $attempt,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attempt_id'       => ['required', 'exists:attempts,id'],
            'selected_options' => ['nullable', 'array'],
            'arranged_order'   => ['nullable', 'array'],
            'filled_blanks'    => ['nullable', 'array'],
            'time_taken'       => ['nullable', 'integer'],
        ]);

        $attempt = Attempt::where('id', $validated['attempt_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $answer = AttemptAnswer::create([
            'attempt_id'       => $attempt->id,
            'selected_options' => $validated['selected_options'] ?? null,
            'arranged_order'   => $validated['arranged_order'] ?? null,
            'filled_blanks'    => $validated['filled_blanks'] ?? null,
        ]);

        $attempt->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
            'time_taken'   => $validated['time_taken'] ?? null,
        ]);

        $this->scoring->scoreReading($attempt, $answer);
        $attempt->update(['status' => 'scored']);

        return redirect()->route('student.reading.results', $attempt);
    }

    public function results(Attempt $attempt): Response
    {
        $this->authorize('view', $attempt);
        $attempt->load(['question.questionType', 'question.options', 'answer', 'score']);

        return Inertia::render('Student/Reading/Results', [
            'attempt' => $attempt,
        ]);
    }
}
