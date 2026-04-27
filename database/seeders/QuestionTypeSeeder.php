<?php

namespace Database\Seeders;

use App\Models\QuestionType;
use Illuminate\Database\Seeder;

class QuestionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // ─── SPEAKING ─────────────────────────────────────────────────────
            [
                'module'                 => 'speaking',
                'name'                   => 'Read Aloud',
                'slug'                   => 'read-aloud',
                'description'            => 'Read the text aloud as naturally as possible.',
                'instructions'           => 'Look at the text below. In 40 seconds, you must read this text aloud as naturally and clearly as possible. You have 40 seconds to read aloud.',
                'prep_time'              => 40,
                'response_time'          => 40,
                'requires_audio_response'=> true,
                'sort_order'             => 1,
            ],
            [
                'module'                 => 'speaking',
                'name'                   => 'Repeat Sentence',
                'slug'                   => 'repeat-sentence',
                'description'            => 'Repeat the sentence you hear.',
                'instructions'           => 'You will hear a sentence. Please repeat the sentence exactly as you hear it. You will hear the sentence only once.',
                'prep_time'              => 0,
                'response_time'          => 15,
                'requires_audio_input'   => true,
                'requires_audio_response'=> true,
                'sort_order'             => 2,
            ],
            [
                'module'                 => 'speaking',
                'name'                   => 'Describe Image',
                'slug'                   => 'describe-image',
                'description'            => 'Describe the image in detail.',
                'instructions'           => 'Look at the image below. In 25 seconds, please speak into the microphone and describe in detail what the image is showing. You will have 40 seconds to give your response.',
                'prep_time'              => 25,
                'response_time'          => 40,
                'requires_audio_response'=> true,
                'sort_order'             => 3,
            ],
            [
                'module'                 => 'speaking',
                'name'                   => 'Re-tell Lecture',
                'slug'                   => 'retell-lecture',
                'description'            => 'Re-tell the lecture in your own words.',
                'instructions'           => 'You will hear a lecture. After listening, in 10 seconds, please speak into the microphone and retell what you have just heard from the lecture in your own words. You will have 40 seconds to give your response.',
                'prep_time'              => 10,
                'response_time'          => 40,
                'requires_audio_input'   => true,
                'requires_audio_response'=> true,
                'sort_order'             => 4,
            ],
            [
                'module'                 => 'speaking',
                'name'                   => 'Answer Short Question',
                'slug'                   => 'answer-short-question',
                'description'            => 'Answer the question with a word or short phrase.',
                'instructions'           => 'You will hear a question. Please give a simple and short answer. Often just one or a few words is enough.',
                'prep_time'              => 0,
                'response_time'          => 10,
                'requires_audio_input'   => true,
                'requires_audio_response'=> true,
                'sort_order'             => 5,
            ],

            // ─── WRITING ──────────────────────────────────────────────────────
            [
                'module'                => 'writing',
                'name'                  => 'Summarize Written Text',
                'slug'                  => 'summarize-written-text',
                'description'           => 'Read the passage and summarize it in one sentence.',
                'instructions'          => 'Read the passage below and summarize it using one sentence. Type your response in the box at the bottom of the screen. You have 10 minutes to finish this task. Your response will be judged on the quality of your writing and on how well your response presents the key points in the passage.',
                'response_time'         => 600,
                'word_limit_min'        => 5,
                'word_limit_max'        => 75,
                'requires_text_response'=> true,
                'sort_order'            => 1,
            ],
            [
                'module'                => 'writing',
                'name'                  => 'Write Essay',
                'slug'                  => 'write-essay',
                'description'           => 'Write an essay on the given topic.',
                'instructions'          => 'You will have 20 minutes to plan, write and revise an essay about the topic below. Your response will be judged on how well you develop a position, organize your ideas, present supporting details, and control the elements of standard written English.',
                'response_time'         => 1200,
                'word_limit_min'        => 200,
                'word_limit_max'        => 300,
                'requires_text_response'=> true,
                'sort_order'            => 2,
            ],

            // ─── READING ──────────────────────────────────────────────────────
            [
                'module'       => 'reading',
                'name'         => 'Multiple Choice (Single)',
                'slug'         => 'reading-multiple-choice-single',
                'description'  => 'Choose the best answer from the options.',
                'instructions' => 'Read the text and answer the multiple-choice question by selecting the correct response. Only one response is correct.',
                'has_options'  => true,
                'sort_order'   => 1,
            ],
            [
                'module'       => 'reading',
                'name'         => 'Multiple Choice (Multiple)',
                'slug'         => 'reading-multiple-choice-multiple',
                'description'  => 'Choose all correct answers.',
                'instructions' => 'Read the text and answer the question by selecting all the correct responses. More than one response is correct.',
                'has_options'  => true,
                'sort_order'   => 2,
            ],
            [
                'module'      => 'reading',
                'name'        => 'Re-order Paragraphs',
                'slug'        => 'reorder-paragraphs',
                'description' => 'Restore the original order of the text.',
                'instructions'=> 'The text boxes in the left panel have been placed in a random order. Restore the original order by dragging the text boxes from the left panel to the right panel.',
                'sort_order'  => 3,
            ],
            [
                'module'      => 'reading',
                'name'        => 'Fill in the Blanks (Reading)',
                'slug'        => 'reading-fill-blanks',
                'description' => 'Drag words from the box to fill the blanks.',
                'instructions'=> 'In the text below some words are missing. Drag words from the box below to the appropriate place in the text. To undo an answer choice, drag the word back to the box below the text.',
                'sort_order'  => 4,
            ],
            [
                'module'      => 'reading',
                'name'        => 'Reading & Writing Fill in the Blanks',
                'slug'        => 'reading-writing-fill-blanks',
                'description' => 'Select the correct word from dropdown menus.',
                'instructions'=> 'Below is a text with blanks. Click on each blank, a list of choices will appear. Select the appropriate answer choice for each blank.',
                'sort_order'  => 5,
            ],

            // ─── LISTENING ────────────────────────────────────────────────────
            [
                'module'                => 'listening',
                'name'                  => 'Summarize Spoken Text',
                'slug'                  => 'summarize-spoken-text',
                'description'           => 'Write a summary of the audio recording.',
                'instructions'          => 'You will hear a short report. Write a summary for a fellow student who was not present. You should write 50-70 words. You have 10 minutes to finish this task.',
                'response_time'         => 600,
                'word_limit_min'        => 50,
                'word_limit_max'        => 70,
                'requires_audio_input'  => true,
                'requires_text_response'=> true,
                'sort_order'            => 1,
            ],
            [
                'module'              => 'listening',
                'name'                => 'Multiple Choice (Multiple)',
                'slug'                => 'listening-multiple-choice-multiple',
                'description'         => 'Choose all correct answers after listening.',
                'instructions'        => 'Listen to the recording and answer the question by selecting all the correct responses.',
                'requires_audio_input'=> true,
                'has_options'         => true,
                'sort_order'          => 2,
            ],
            [
                'module'                => 'listening',
                'name'                  => 'Fill in the Blanks',
                'slug'                  => 'listening-fill-blanks',
                'description'           => 'Type the missing words as you listen.',
                'instructions'          => 'You will hear a recording. Type the missing words in each blank.',
                'requires_audio_input'  => true,
                'requires_text_response'=> true,
                'sort_order'            => 3,
            ],
            [
                'module'              => 'listening',
                'name'                => 'Highlight Correct Summary',
                'slug'                => 'highlight-correct-summary',
                'description'         => 'Select the paragraph that best summarizes the recording.',
                'instructions'        => 'You will hear a recording. Click on the paragraph that best relates to the recording.',
                'requires_audio_input'=> true,
                'has_options'         => true,
                'sort_order'          => 4,
            ],
            [
                'module'              => 'listening',
                'name'                => 'Multiple Choice (Single)',
                'slug'                => 'listening-multiple-choice-single',
                'description'         => 'Choose the best answer after listening.',
                'instructions'        => 'Listen to the recording and answer the multiple-choice question by selecting the correct response.',
                'requires_audio_input'=> true,
                'has_options'         => true,
                'sort_order'          => 5,
            ],
            [
                'module'              => 'listening',
                'name'                => 'Select Missing Word',
                'slug'                => 'select-missing-word',
                'description'         => 'Predict the missing word from context.',
                'instructions'        => 'You will hear a recording about [topic]. At the end of the recording the last word or group of words has been replaced by a beep. Select the correct option to complete the recording.',
                'requires_audio_input'=> true,
                'has_options'         => true,
                'sort_order'          => 6,
            ],
            [
                'module'              => 'listening',
                'name'                => 'Highlight Incorrect Words',
                'slug'                => 'highlight-incorrect-words',
                'description'         => 'Click on the words that differ from the recording.',
                'instructions'        => 'You will hear a recording. Below is a transcription of the recording. Some words in the transcription differ from what the speaker said. Please click on the words that are different.',
                'requires_audio_input'=> true,
                'sort_order'          => 7,
            ],
            [
                'module'                => 'listening',
                'name'                  => 'Write from Dictation',
                'slug'                  => 'write-from-dictation',
                'description'           => 'Type the sentence you hear exactly.',
                'instructions'          => 'You will hear a sentence. Type the sentence in the box below exactly as you hear it. Write as much of the sentence as you can. You will hear the sentence only once.',
                'requires_audio_input'  => true,
                'requires_text_response'=> true,
                'sort_order'            => 8,
            ],
        ];

        foreach ($types as $type) {
            QuestionType::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
