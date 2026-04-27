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
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SpeakingController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function index(): Response
    {
        $tenant = session('tenant');

        $types = QuestionType::where('module', 'speaking')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($type) use ($tenant) {
                return [
                    ...$type->toArray(),
                    'question_count' => Question::where('tenant_id', $tenant->id)
                        ->where('question_type_id', $type->id)
                        ->active()
                        ->count(),
                    'attempts_count' => Attempt::where('user_id', Auth::id())
                        ->whereHas('question', fn($q) => $q->where('question_type_id', $type->id))
                        ->count(),
                ];
            });

        return Inertia::render('Student/Speaking/Index', [
            'questionTypes' => $types,
        ]);
    }

    public function practice(string $type): Response
    {
        $tenant = session('tenant');
        $questionType = QuestionType::where('slug', $type)->where('module', 'speaking')->firstOrFail();

        $questions = Question::with('questionType')
            ->where('tenant_id', $tenant->id)
            ->where('question_type_id', $questionType->id)
            ->active()
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Student/Speaking/Practice', [
            'questionType' => $questionType,
            'questions'    => $questions,
        ]);
    }

    public function question(Question $question): Response
    {
        $question->load('questionType');

        $attempt = Attempt::create([
            'user_id'    => Auth::id(),
            'question_id'=> $question->id,
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);

        return Inertia::render('Student/Speaking/Question', [
            'question' => [
                ...$question->toArray(),
                'image_url' => $question->image_url,
                'audio_url' => $question->audio_url,
            ],
            'attempt'  => $attempt,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attempt_id' => ['required', 'exists:attempts,id'],
            'audio'      => ['required', 'file', 'mimes:webm,ogg,mp4,wav', 'max:20480'],
        ]);

        $attempt = Attempt::where('id', $validated['attempt_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Store audio file
        $path = $request->file('audio')->store('speaking-responses', 'public');
        $duration = $request->integer('duration', 0);

        $answer = AttemptAnswer::create([
            'attempt_id'     => $attempt->id,
            'audio_path'     => $path,
            'audio_duration' => $duration,
        ]);

        $attempt->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
            'time_taken'   => $duration,
        ]);

        // Score the attempt
        $score = $this->scoring->scoreSpeaking($attempt, $answer);

        $attempt->update(['status' => 'scored']);

        return redirect()->route('student.speaking.results', $attempt)
            ->with('success', 'Your response has been submitted and scored!');
    }

    public function results(Attempt $attempt): Response
    {
        $this->authorize('view', $attempt);

        $attempt->load(['question.questionType', 'answer', 'score']);

        return Inertia::render('Student/Speaking/Results', [
            'attempt' => [
                ...$attempt->toArray(),
                'answer' => [
                    ...$attempt->answer?->toArray() ?? [],
                    'audio_url' => $attempt->answer?->audio_url,
                ],
            ],
        ]);
    }
}
