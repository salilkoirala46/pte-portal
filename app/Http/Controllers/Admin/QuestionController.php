<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function index(Request $request): Response
    {
        $tenant = session('tenant');

        $questions = Question::with('questionType')
            ->where('tenant_id', $tenant->id)
            ->when($request->module, fn($q) => $q->forModule($request->module))
            ->when($request->type, fn($q) => $q->ofType($request->type))
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('question_type_id')
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Questions/Index', [
            'questions'     => $questions,
            'questionTypes' => QuestionType::orderBy('module')->orderBy('sort_order')->get(),
            'filters'       => $request->only(['module', 'type', 'search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Questions/Create', [
            'questionTypes' => QuestionType::orderBy('module')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = session('tenant');
        $type   = QuestionType::findOrFail($request->question_type_id);

        $validated = $request->validate([
            'question_type_id'   => ['required', 'exists:question_types,id'],
            'title'              => ['required', 'string', 'max:500'],
            'content'            => ['nullable', 'string'],
            'additional_content' => ['nullable', 'string'],
            'image'              => ['nullable', 'image', 'max:5120'],
            'audio'              => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:20480'],
            'audio_duration'     => ['nullable', 'integer'],
            'blanks'             => ['nullable', 'array'],
            'word_bank'          => ['nullable', 'array'],
            'paragraphs'         => ['nullable', 'array'],
            'correct_answer'     => ['nullable', 'string'],
            'sample_answer'      => ['nullable', 'string'],
            'difficulty'         => ['required', 'in:easy,medium,hard'],
            'options'            => ['nullable', 'array'],
            'options.*.label'    => ['required_with:options', 'string'],
            'options.*.content'  => ['required_with:options', 'string'],
            'options.*.is_correct'=> ['required_with:options', 'boolean'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('questions/images', 'public')
            : null;

        $audioPath = $request->hasFile('audio')
            ? $request->file('audio')->store('questions/audio', 'public')
            : null;

        $question = Question::create([
            ...$validated,
            'tenant_id'  => $tenant->id,
            'image_path' => $imagePath,
            'audio_path' => $audioPath,
        ]);

        if (! empty($validated['options'])) {
            foreach ($validated['options'] as $i => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label'       => $opt['label'],
                    'content'     => $opt['content'],
                    'is_correct'  => $opt['is_correct'],
                    'sort_order'  => $i,
                ]);
            }
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully.');
    }

    public function edit(Question $question): Response
    {
        $question->load('options');

        return Inertia::render('Admin/Questions/Edit', [
            'question'      => [
                ...$question->toArray(),
                'image_url' => $question->image_url,
                'audio_url' => $question->audio_url,
            ],
            'questionTypes' => QuestionType::orderBy('module')->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'title'              => ['required', 'string', 'max:500'],
            'content'            => ['nullable', 'string'],
            'additional_content' => ['nullable', 'string'],
            'image'              => ['nullable', 'image', 'max:5120'],
            'audio'              => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a', 'max:20480'],
            'audio_duration'     => ['nullable', 'integer'],
            'blanks'             => ['nullable', 'array'],
            'word_bank'          => ['nullable', 'array'],
            'paragraphs'         => ['nullable', 'array'],
            'correct_answer'     => ['nullable', 'string'],
            'sample_answer'      => ['nullable', 'string'],
            'difficulty'         => ['required', 'in:easy,medium,hard'],
            'is_active'          => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($question->image_path) Storage::disk('public')->delete($question->image_path);
            $validated['image_path'] = $request->file('image')->store('questions/images', 'public');
        }

        if ($request->hasFile('audio')) {
            if ($question->audio_path) Storage::disk('public')->delete($question->audio_path);
            $validated['audio_path'] = $request->file('audio')->store('questions/audio', 'public');
        }

        $question->update($validated);

        return back()->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        if ($question->image_path) Storage::disk('public')->delete($question->image_path);
        if ($question->audio_path) Storage::disk('public')->delete($question->audio_path);

        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted.');
    }
}
