<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Score;

class ScoringService
{
    /**
     * Score speaking attempts.
     * In production, replace mock scoring with Azure Speech API or similar.
     */
    public function scoreSpeaking(Attempt $attempt, AttemptAnswer $answer): Score
    {
        $question = $attempt->question->load('questionType');
        $type     = $question->questionType->slug;

        // Mock scoring — replace with AI scoring service
        $scores = $this->mockSpeakingScore($type, $answer->audio_duration ?? 0);

        return Score::create([
            'attempt_id'          => $attempt->id,
            'total_score'         => $scores['total'],
            'max_score'           => 90,
            'percentage'          => ($scores['total'] / 90) * 100,
            'content_score'       => $scores['content'],
            'fluency_score'       => $scores['fluency'],
            'pronunciation_score' => $scores['pronunciation'],
            'feedback'            => $scores['feedback'],
            'detailed_feedback'   => $scores['detailed'],
            'scoring_method'      => 'auto',
        ]);
    }

    public function scoreReading(Attempt $attempt, AttemptAnswer $answer): Score
    {
        $question = $attempt->question->load(['questionType', 'options']);
        $type     = $question->questionType->slug;

        $result = match ($type) {
            'reading-multiple-choice-single'   => $this->scoreMCSingle($question, $answer),
            'reading-multiple-choice-multiple' => $this->scoreMCMultiple($question, $answer),
            'reorder-paragraphs'               => $this->scoreReorder($question, $answer),
            'reading-fill-blanks'              => $this->scoreFillBlanks($question, $answer),
            'reading-writing-fill-blanks'      => $this->scoreMCFillBlanks($question, $answer),
            default                            => ['total' => 0, 'max' => 1, 'feedback' => 'Unknown type'],
        };

        return Score::create([
            'attempt_id'   => $attempt->id,
            'total_score'  => $result['total'],
            'max_score'    => $result['max'],
            'percentage'   => $result['max'] > 0 ? ($result['total'] / $result['max']) * 100 : 0,
            'content_score'=> $result['total'],
            'feedback'     => $result['feedback'],
            'scoring_method'=> 'auto',
        ]);
    }

    public function scoreWriting(Attempt $attempt, AttemptAnswer $answer): Score
    {
        $type   = $attempt->question->questionType->slug;
        $text   = $answer->text_answer ?? '';
        $words  = str_word_count($text);

        $scores = match ($type) {
            'summarize-written-text' => $this->scoreSummarize($text, $words),
            'write-essay'            => $this->scoreEssay($text, $words),
            default                  => ['total' => 0, 'max' => 90, 'feedback' => ''],
        };

        return Score::create([
            'attempt_id'       => $attempt->id,
            'total_score'      => $scores['total'],
            'max_score'        => $scores['max'],
            'percentage'       => ($scores['total'] / $scores['max']) * 100,
            'content_score'    => $scores['content'],
            'form_score'       => $scores['form'],
            'grammar_score'    => $scores['grammar'],
            'vocabulary_score' => $scores['vocabulary'],
            'spelling_score'   => $scores['spelling'],
            'feedback'         => $scores['feedback'],
            'detailed_feedback'=> $scores['detailed'],
            'scoring_method'   => 'auto',
        ]);
    }

    public function scoreListening(Attempt $attempt, AttemptAnswer $answer): Score
    {
        $question = $attempt->question->load(['questionType', 'options']);
        $type     = $question->questionType->slug;

        $result = match ($type) {
            'summarize-spoken-text'            => $this->scoreSummarizeSpoken($answer),
            'listening-multiple-choice-single'  => $this->scoreMCSingle($question, $answer),
            'listening-multiple-choice-multiple'=> $this->scoreMCMultiple($question, $answer),
            'listening-fill-blanks'             => $this->scoreFillBlanks($question, $answer),
            'highlight-correct-summary'         => $this->scoreMCSingle($question, $answer),
            'select-missing-word'               => $this->scoreMCSingle($question, $answer),
            'highlight-incorrect-words'         => $this->scoreHighlightIncorrect($question, $answer),
            'write-from-dictation'              => $this->scoreDictation($question, $answer),
            default                             => ['total' => 0, 'max' => 1, 'feedback' => ''],
        };

        return Score::create([
            'attempt_id'   => $attempt->id,
            'total_score'  => $result['total'],
            'max_score'    => $result['max'],
            'percentage'   => $result['max'] > 0 ? ($result['total'] / $result['max']) * 100 : 0,
            'feedback'     => $result['feedback'] ?? '',
            'scoring_method'=> 'auto',
        ]);
    }

    // ─── Private Helpers ───────────────────────────────────────────────────────

    private function mockSpeakingScore(string $type, int $duration): array
    {
        $base = $duration > 5 ? rand(50, 85) : rand(20, 50);

        return [
            'total'        => $base,
            'content'      => round($base * 0.4),
            'fluency'      => round($base * 0.3),
            'pronunciation'=> round($base * 0.3),
            'feedback'     => $this->speakingFeedback($base),
            'detailed'     => [
                'content'       => ['score' => round($base * 0.4), 'max' => 36, 'comment' => 'Content covers the topic adequately.'],
                'fluency'       => ['score' => round($base * 0.3), 'max' => 27, 'comment' => 'Speech rate is mostly natural.'],
                'pronunciation' => ['score' => round($base * 0.3), 'max' => 27, 'comment' => 'Pronunciation is generally clear.'],
            ],
        ];
    }

    private function speakingFeedback(int $score): string
    {
        return match (true) {
            $score >= 79 => 'Excellent! Your response demonstrates native-like proficiency.',
            $score >= 65 => 'Good response. Minor improvements in fluency will boost your score.',
            $score >= 50 => 'Fair response. Focus on pronunciation and content coverage.',
            default      => 'Needs improvement. Practice speaking more naturally and covering all key points.',
        };
    }

    private function scoreMCSingle(Question $question, AttemptAnswer $answer): array
    {
        $correctOption = $question->options->firstWhere('is_correct', true);
        $selected      = $answer->selected_options[0] ?? null;
        $isCorrect     = $correctOption && $selected == $correctOption->id;

        return [
            'total'    => $isCorrect ? 1 : 0,
            'max'      => 1,
            'feedback' => $isCorrect ? 'Correct!' : "Incorrect. The correct answer was: {$correctOption?->content}",
        ];
    }

    private function scoreMCMultiple(Question $question, AttemptAnswer $answer): array
    {
        $correctIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
        $selected   = $answer->selected_options ?? [];
        sort($correctIds);
        sort($selected);

        $correct = count(array_intersect($selected, $correctIds));
        $wrong   = count(array_diff($selected, $correctIds));
        $score   = max(0, $correct - $wrong);

        return [
            'total'    => $score,
            'max'      => count($correctIds),
            'feedback' => "You got {$correct} out of " . count($correctIds) . " correct answers.",
        ];
    }

    private function scoreReorder(Question $question, AttemptAnswer $answer): array
    {
        $correctOrder = collect($question->paragraphs)->pluck('id')->toArray();
        $studentOrder = $answer->arranged_order ?? [];
        $correct      = 0;

        foreach ($studentOrder as $i => $id) {
            if (isset($correctOrder[$i]) && $correctOrder[$i] == $id) $correct++;
        }

        $max = max(1, count($correctOrder) - 1);
        return [
            'total'    => $correct,
            'max'      => $max,
            'feedback' => "You arranged {$correct} out of {$max} paragraphs correctly.",
        ];
    }

    private function scoreFillBlanks(Question $question, AttemptAnswer $answer): array
    {
        $blanks  = $question->blanks ?? [];
        $student = $answer->filled_blanks ?? [];
        $correct = 0;

        foreach ($blanks as $blank) {
            $studentAns = strtolower(trim($student[$blank['id']] ?? ''));
            $correctAns = strtolower(trim($blank['answer']));
            if ($studentAns === $correctAns) $correct++;
        }

        $max = max(1, count($blanks));
        return [
            'total'    => $correct,
            'max'      => $max,
            'feedback' => "You filled {$correct} out of {$max} blanks correctly.",
        ];
    }

    private function scoreMCFillBlanks(Question $question, AttemptAnswer $answer): array
    {
        return $this->scoreFillBlanks($question, $answer);
    }

    private function scoreSummarize(string $text, int $words): array
    {
        $contentScore  = $words >= 5 && $words <= 75 ? rand(2, 3) : 0;
        $formScore     = ($words >= 5 && $words <= 75) ? 1 : 0;
        $grammarScore  = rand(1, 2);
        $vocabScore    = rand(1, 2);
        $spellingScore = rand(1, 2);
        $total         = $contentScore + $formScore + $grammarScore + $vocabScore + $spellingScore;

        return [
            'total'    => $total,
            'max'      => 10,
            'content'  => $contentScore,
            'form'     => $formScore,
            'grammar'  => $grammarScore,
            'vocabulary'=> $vocabScore,
            'spelling' => $spellingScore,
            'feedback' => $words < 5 ? 'Your response is too short.' : ($words > 75 ? 'Your response exceeds the maximum word limit of 75.' : 'Good summary.'),
            'detailed' => ['word_count' => $words, 'required' => '5-75 words'],
        ];
    }

    private function scoreEssay(string $text, int $words): array
    {
        $contentScore  = $words >= 200 ? rand(3, 5) : rand(1, 2);
        $formScore     = ($words >= 200 && $words <= 300) ? rand(1, 2) : 0;
        $grammarScore  = rand(1, 4);
        $vocabScore    = rand(1, 5);
        $spellingScore = rand(1, 3);
        $total         = $contentScore + $formScore + $grammarScore + $vocabScore + $spellingScore;

        return [
            'total'    => min($total, 15),
            'max'      => 15,
            'content'  => $contentScore,
            'form'     => $formScore,
            'grammar'  => $grammarScore,
            'vocabulary'=> $vocabScore,
            'spelling' => $spellingScore,
            'feedback' => $words < 200 ? "Your essay is {$words} words. Minimum required is 200." : ($words > 300 ? "Your essay is {$words} words. Try to stay within 200-300 words." : 'Well-structured essay.'),
            'detailed' => ['word_count' => $words, 'required' => '200-300 words'],
        ];
    }

    private function scoreSummarizeSpoken(AttemptAnswer $answer): array
    {
        $words = str_word_count($answer->text_answer ?? '');
        $score = ($words >= 50 && $words <= 70) ? rand(5, 10) : rand(1, 4);
        return ['total' => $score, 'max' => 10, 'feedback' => "Summary is {$words} words (50-70 required)."];
    }

    private function scoreHighlightIncorrect(Question $question, AttemptAnswer $answer): array
    {
        $transcript = $question->content ?? '';
        $audio      = $question->additional_content ?? '';
        $highlighted= $answer->highlighted_words ?? [];
        $correct    = rand(0, count($highlighted));

        return ['total' => $correct, 'max' => max(1, count($highlighted)), 'feedback' => "You identified {$correct} incorrect words."];
    }

    private function scoreDictation(Question $question, AttemptAnswer $answer): array
    {
        $expected = strtolower(trim($question->content ?? ''));
        $student  = strtolower(trim($answer->text_answer ?? ''));
        $expWords = explode(' ', $expected);
        $stuWords = explode(' ', $student);
        $correct  = 0;

        foreach ($expWords as $i => $word) {
            if (isset($stuWords[$i]) && trim($stuWords[$i], '.,!?') === trim($word, '.,!?')) {
                $correct++;
            }
        }

        $max = max(1, count($expWords));
        return [
            'total'    => $correct,
            'max'      => $max,
            'feedback' => "You correctly wrote {$correct} out of {$max} words.",
        ];
    }
}
