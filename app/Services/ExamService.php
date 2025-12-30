<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Flashcard;
use App\Models\Set;
use App\Models\ExamQuestion;
use Illuminate\Support\Str;

class ExamService
{
    public function config(string $difficulty): array
    {
        return match ($difficulty) {
            'easy' => ['count' => 5, 'time' => 600],
            'normal' => ['count' => 10, 'time' => 1200],
            'hard' => ['count' => 20, 'time' => 1800],
        };
    }

    public function start(array $setIds, string $difficulty): Exam
    {
        $config = $this->config($difficulty);

        $flashcards = Flashcard::whereIn('set_id', $setIds)->get();

        $sets = Set::whereIn('id', $setIds)
            ->pluck('name')
            ->implode(' - ');

        $selected = $flashcards
            ->shuffle()
            ->take($config['count'])
            ->values();

        $exam = Exam::create([
            'uuid' => (string) Str::uuid(),
            'difficulty' => $difficulty,
            'total_questions' => $config['count'],
            'time_limit' => $config['time'],
            'started_at' => now(),
            'sets' => $sets,
        ]);

        foreach ($selected as $index => $flashcard) {
            ExamQuestion::create([
                'exam_id' => $exam->id,
                'flashcard_id' => $flashcard->id,
                'order' => $index + 1,
            ]);
        }

        return $exam;
    }

    public function autoFinishIfTimeExceeded(Exam $exam): void
    {
        if ($exam->finished_at) {
            return;
        }

        $end = $exam->started_at->addSeconds($exam->time_limit);

        if (now()->greaterThanOrEqualTo($end)) {
            $exam->update(['finished_at' => now()]);
        }
    }
}
