<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('uuid')->unique();
            $table->string('sets');
            $table->enum('difficulty', ['easy', 'normal', 'hard']);
            $table->unsignedInteger('total_questions');
            $table->timestamp('started_at')->nullable();
            $table->integer('time_limit');
            $table->timestamp('finished_at')->nullable();
        });

        Schema::create('exam_questions', function (Blueprint $table) {
             $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flashcard_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order');
            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'flashcard_id']);
        });
        Schema::create('smart_learn_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedInteger('total');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
        Schema::create('smart_learn_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smart_learn_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('flashcard_id')->constrained();
            $table->unsignedInteger('order');
            $table->foreignId('selected_option_id')->nullable()->constrained('flashcard_options')->nullOnDelete();
            $table->boolean('is_correct')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['smart_learn_session_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('smart_learn_sessions');
        Schema::dropIfExists('smart_learn_questions');
    }
};
