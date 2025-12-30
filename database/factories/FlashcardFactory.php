<?php

namespace Database\Factories;

use App\Models\Flashcard;
use App\Models\FlashcardOption;
use App\Models\Set;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FlashcardFactory extends Factory
{
    protected $model = Flashcard::class;

    public function definition(): array
    {
        return [
            'set_id' => Set::factory(),
            'question' => $this->faker->sentence(6) . '?',
            'answer' => $this->faker->sentence(10),
            'notes' => $this->faker->optional()->paragraph,
            'type' => $this->faker->randomElement(['flip', 'write', 'choice']),
            'learned' => $this->faker->boolean(30),
            'times_shown' => $this->faker->numberBetween(0, 20),
            'image_path' => $this->faker->optional()->image('storage/app/public/flashcards', 640, 480, null, false),
            'times_correct' => $this->faker->numberBetween(0, 15),
            'last_seen_at' => $this->faker->optional()->dateTimeBetween('-14 days', 'now'),
        ];
    }

    public function learned(): static
    {
        return $this->state(fn () => [
            'learned' => true,
            'times_shown' => rand(5, 20),
            'times_correct' => rand(4, 20),
        ]);
    }

    public function fresh(): static
    {
        return $this->state(fn () => [
            'learned' => false,
            'times_shown' => 0,
            'times_correct' => 0,
            'last_seen_at' => null,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Flashcard $flashcard) {
            $question = Str::lower($flashcard->question);

            $simplePrefixes = [
                'czy ', 'do ', 'did ', 'is ', 'are ', 'was ', 'were ', 'has ', 'have '
            ];

            $isSimple = collect($simplePrefixes)
                ->contains(fn ($p) => Str::startsWith($question, $p));

            if ($isSimple) {
                $this->createYesNoOptions($flashcard);
            } else {
                $this->createComplexOptions($flashcard);
            }
        });
    }

    protected function createYesNoOptions(Flashcard $flashcard): void
    {
        $yes = collect(['Tak', 'Yes'])->random();
        $no  = collect(['Nie', 'No'])->random();

        $correctIsYes = $this->faker->boolean();

        FlashcardOption::create([
            'flashcard_id' => $flashcard->id,
            'text' => $yes,
            'is_correct' => $correctIsYes,
        ]);

        FlashcardOption::create([
            'flashcard_id' => $flashcard->id,
            'text' => $no,
            'is_correct' => ! $correctIsYes,
        ]);
    }

    protected function createComplexOptions(Flashcard $flashcard): void
    {
        // poprawna
        FlashcardOption::create([
            'flashcard_id' => $flashcard->id,
            'text' => $flashcard->answer,
            'is_correct' => true,
        ]);

        // 3 niepoprawne
        for ($i = 0; $i < 3; $i++) {
            FlashcardOption::create([
                'flashcard_id' => $flashcard->id,
                'text' => 'Wrong answer ' . Str::random(5),
                'is_correct' => false,
            ]);
        }
    }
}
