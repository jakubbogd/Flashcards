<?php

namespace Database\Factories;

use App\Models\SmartLearnSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SmartLearnSessionFactory extends Factory
{
    protected $model = SmartLearnSession::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'finished_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'total' => $this->faker->numberBetween(5, 20),
        ];
    }
}
