<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_type_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();           // Main passage/text
            $table->longText('additional_content')->nullable();// Secondary text if needed
            $table->string('image_path')->nullable();          // For Describe Image
            $table->string('audio_path')->nullable();          // For listening questions
            $table->integer('audio_duration')->nullable();     // seconds
            $table->json('blanks')->nullable();                // For fill-in-the-blank: [{"id":1,"answer":"word"}]
            $table->json('word_bank')->nullable();             // Words to drag into blanks
            $table->json('paragraphs')->nullable();            // For re-order paragraphs
            $table->string('correct_answer')->nullable();      // For single answer questions
            $table->text('sample_answer')->nullable();         // Model answer for writing/speaking
            $table->json('scoring_rubric')->nullable();        // Scoring criteria
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
