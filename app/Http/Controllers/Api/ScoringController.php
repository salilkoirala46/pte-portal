<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Services\ScoringService;
use Illuminate\Http\Request;

class ScoringController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function scoreSpeeking(Request $request)
    {
        $attempt = Attempt::findOrFail($request->attempt_id);
        $answer  = $attempt->answer;

        $score = $this->scoring->scoreSpeaking($attempt, $answer);

        return response()->json(['score' => $score]);
    }

    public function scoreWriting(Request $request)
    {
        $attempt = Attempt::findOrFail($request->attempt_id);
        $answer  = $attempt->answer;

        $score = $this->scoring->scoreWriting($attempt, $answer);

        return response()->json(['score' => $score]);
    }
}
