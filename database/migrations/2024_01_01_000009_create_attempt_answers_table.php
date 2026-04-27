<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained()->cascadeOnDelete();
            $table->longText('text_answer')->nullable();       // For writing/dictation
            $table->string('audio_path')->nullable();          // For speaking
            $table->integer('audio_duration')->nullable();     // seconds
            $table->json('selected_options')->nullable();      // For MCQ: [option_id, ...]
            $table->json('arranged_order')->nullable();        // For re-order paragraphs
            $table->json('filled_blanks')->nullable();         // {"1":"word","2":"word"}
            $table->json('highlighted_words')->nullable();     // For highlight incorrect words
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
    }
};
