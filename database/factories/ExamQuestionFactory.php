<?php

namespace Database\Factories;

use App\Models\ExamQuestion;
use App\Models\Exam;
use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamQuestionFactory extends Factory
{
    protected $model = ExamQuestion::class;

    public function definition(): array
    {
        return [
            'exam_id' => Exam::factory(),
            'flashcard_id' => Flashcard::factory(),
            'order' => 1,
            'is_correct' => null,
            'answered_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
