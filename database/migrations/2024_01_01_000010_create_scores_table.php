<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(90);
            $table->decimal('percentage', 5, 2)->default(0);

            // PTE specific scores
            $table->decimal('content_score', 5, 2)->nullable();
            $table->decimal('form_score', 5, 2)->nullable();
            $table->decimal('grammar_score', 5, 2)->nullable();
            $table->decimal('vocabulary_score', 5, 2)->nullable();
            $table->decimal('spelling_score', 5, 2)->nullable();
            $table->decimal('fluency_score', 5, 2)->nullable();
            $table->decimal('pronunciation_score', 5, 2)->nullable();

            $table->text('feedback')->nullable();
            $table->json('detailed_feedback')->nullable();
            $table->enum('scoring_method', ['auto', 'ai', 'manual'])->default('auto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
