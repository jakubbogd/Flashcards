<?php

namespace App\Services;

use App\Models\SmartLearnSession;
use App\Models\SmartLearnQuestion;

class SmartLearnAnswerService
{
    public function answer(
        SmartLearnSession $session,
        SmartLearnQuestion $question,
        array $data
    ): bool {
        $flashcard = $question->flashcard;

        $flashcard->increment('times_shown');

        $isCorrect = $flashcard->answer === $data['option_txt'];

        if ($isCorrect) {
            $flashcard->increment(
                'times_correct',
                $data['mode'] === 'write' ? 3 : 1
            );
        }

        $flashcard->update([
            'last_seen_at' => now(),
        ]);

        $question->update([
            'selected_option_id' => $data['mode'] === 'multiple_choice'
                ? $data['option_id']
                : null,
            'is_correct' => $isCorrect,
            'answered_at' => now(),
        ]);

        if ($session->questions()->whereNull('answered_at')->count() === 0) {
            $session->update(['finished_at' => now()]);
        }

        return $isCorrect;
    }
}
