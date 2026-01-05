<?php

namespace Database\Factories;

use App\Models\Set;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

class SetFactory extends Factory
{
    protected $model = Set::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->sentence(),
            'folder_id' => Folder::factory(),
        ];
    }
}
