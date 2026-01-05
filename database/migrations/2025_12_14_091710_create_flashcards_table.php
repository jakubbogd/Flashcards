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
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('set_id')
                ->constrained()
               ->cascadeOnDelete();
            $table->string('question');
            $table->string('answer');
            $table->text('notes')->nullable()->after('answer');
            $table->string('type')->default('text');
            $table->json('options')->nullable();
            $table->boolean('learned')->default(false);
            $table->unsignedInteger('times_shown')->default(0);
            $table->unsignedInteger('times_correct')->default(0);
            $table->timestamp('last_seen_at')->nullable();
             $table->string('image_path')->nullable()->after('notes');
        });

        Schema::create('flashcard_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashcard_id')
                ->constrained()
               ->cascadeOnDelete();
            $table->string('text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::create('sets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->foreignId('folder_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });

        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcards');
        Schema::dropIfExists('flashcard_options');
        Schema::dropIfExists('sets');
        Schema::dropIfExists('folders');
    }
};
