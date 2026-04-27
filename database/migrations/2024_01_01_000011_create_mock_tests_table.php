<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('duration_minutes')->default(180);
            $table->timestamps();
        });

        Schema::create('mock_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('mock_test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mock_test_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['in_progress', 'submitted', 'scored'])->default('in_progress');
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->decimal('speaking_score', 5, 2)->nullable();
            $table->decimal('reading_score', 5, 2)->nullable();
            $table->decimal('writing_score', 5, 2)->nullable();
            $table->decimal('listening_score', 5, 2)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_test_attempts');
        Schema::dropIfExists('mock_test_questions');
        Schema::dropIfExists('mock_tests');
    }
};
