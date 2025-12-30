<?php

namespace Database\Factories;

use App\Models\SmartLearnQuestion;
use App\Models\SmartLearnSession;
use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmartLearnQuestionFactory extends Factory
{
    protected $model = SmartLearnQuestion::class;

    public function definition(): array
    {
        return [
            'smart_learn_session_id' => SmartLearnSession::factory(),
            'flashcard_id' => Flashcard::factory(),
            'order' => 1,
            'selected_option_id' => null,
            'is_correct' => null,
            'answered_at' => null,
        ];
    }
}
