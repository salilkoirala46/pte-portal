<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionApiController extends Controller
{
    public function streamAudio(Question $question): StreamedResponse
    {
        abort_unless($question->audio_path, 404);

        $disk = config('filesystems.default');
        abort_unless(Storage::disk($disk)->exists($question->audio_path), 404);

        $path = Storage::disk($disk)->path($question->audio_path);
        $mime = mime_content_type($path) ?: 'audio/mpeg';

        return response()->stream(function () use ($path) {
            readfile($path);
        }, 200, [
            'Content-Type'  => $mime,
            'Accept-Ranges' => 'bytes',
        ]);
    }
}
