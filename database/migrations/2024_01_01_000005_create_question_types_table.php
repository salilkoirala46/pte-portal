<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_types', function (Blueprint $table) {
            $table->id();
            $table->enum('module', ['speaking', 'reading', 'writing', 'listening']);
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('prep_time')->default(0);         // seconds
            $table->integer('response_time')->default(0);     // seconds
            $table->integer('word_limit_min')->nullable();
            $table->integer('word_limit_max')->nullable();
            $table->boolean('requires_audio_input')->default(false);
            $table->boolean('requires_audio_response')->default(false);
            $table->boolean('requires_text_response')->default(false);
            $table->boolean('has_options')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_types');
    }
};
