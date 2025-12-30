<?php

namespace App\Services;

use App\Models\Flashcard;
use App\Models\SmartLearnSession;
use App\Models\SmartLearnQuestion;
use Illuminate\Support\Str;

class SmartLearnService
{
    /**
     * Wybiera fiszki do sesji według Spaced Repetition
     *
     * @param array $flashcards kolekcja fiszek
     * @param int $count ile fiszek w sesji
     * @return \Illuminate\Support\Collection
     */
    public function selectFlashcards(array $flashcards, int $count)
    {
        // Obliczamy weight dla każdej fiszki
        foreach ($flashcards as $flashcard) {
            // im mniej razy poprawnie rozwiązana, tym większa waga
            $correct = $flashcard->times_correct ?? 0;
            $shown = $flashcard->times_shown ?? 0;
            $last = $flashcard->last_seen_at ? strtotime($flashcard->last_seen_at) : 0;
            $now = time();

            // prosty wzór: więcej czasu od ostatniego widzenia + niska skuteczność
            $weight = max(1, ($shown - $correct + 1) * ($now - $last + 3600) / 3600);
            $flashcard->weight = $weight;
        }

        // sortujemy według wagi malejąco
        usort($flashcards, fn($a, $b) => $b->weight <=> $a->weight);

        // wybieramy top $count fiszek
        return array_slice($flashcards, 0, $count);
    }

    /**
     * Tworzy nową sesję Smart Learn z wybranymi fiszkami
     */
    public function createSession(array $flashcards, int $count)
    {
        $selected = $this->selectFlashcards($flashcards, $count);

        $session = SmartLearnSession::create([
            'uuid' => (string) Str::uuid(),
            'total' => count($selected),
            'started_at' => now(),
        ]);

        foreach ($selected as $index => $flashcard) {
            SmartLearnQuestion::create([
                'smart_learn_session_id' => $session->id,
                'flashcard_id' => $flashcard->id,
                'order' => $index + 1,
            ]);
        }

        return $session;
    }
}
