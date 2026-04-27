<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'audio'    => ['required', 'file', 'mimes:webm,ogg,mp4,wav', 'max:20480'],
            'attempt_id'=> ['required', 'exists:attempts,id'],
        ]);

        $path = $request->file('audio')->store('speaking-responses', 'public');

        return response()->json(['path' => $path, 'url' => asset('storage/' . $path)]);
    }

    public function stream(AttemptAnswer $recording)
    {
        $this->authorize('view', $recording->attempt);
        return Storage::disk('public')->response($recording->audio_path);
    }
}
