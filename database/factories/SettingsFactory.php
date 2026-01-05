<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Settings;
use App\Models\User;

class SettingsFactory extends Factory
{
    protected $model = Settings::class;

    public function definition()
    {
        return [
            'dark_mode' => $this->faker->boolean(),
            'user_id' => User::factory(),
        ];
    }
}
