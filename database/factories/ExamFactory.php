<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Flashcard;
use App\Models\ExamQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition(): array
    {
        // losowa liczba zestawów
        $sets = ['test1', 'abc', 'demo'];
        $chosenSets = $this->faker->randomElements($sets, 2);
        return [
            'uuid' => Str::uuid(),
            'sets' => implode(' - ', $sets),
            'difficulty' => $this->faker->randomElement(['easy', 'normal', 'hard']),
            'total_questions' => $this->faker->numberBetween(5, 20),
            'time_limit' => $this->faker->numberBetween(300, 1800), // w sekundach
            'started_at' => now(),
            'finished_at' => null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Exam $exam) {
            // Tworzymy flashcards dla tego egzaminu
            $flashcards = Flashcard::factory()
                ->count($exam->total_questions)
                ->create();

            // Tworzymy wpisy w pivot table exam_questions
            foreach ($flashcards as $index => $flashcard) {
                ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'flashcard_id' => $flashcard->id,
                    'order' => $index + 1,
                    'is_correct' => null,
                    'answered_at' => null,
                ]);
            }
        });
    }
}
