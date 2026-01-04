<?php

namespace App\Services;

use App\Models\Flashcard;
use App\Models\FlashcardOption;

class FlashcardService
{
    public function determineType(string $question): string
    {
        if (str_starts_with(strtolower($question), 'czy ')) {
            return 'boolean';
        } else {
            return 'text';
        }
    }

    public function generateOptions(Flashcard $flashcard): void
    {
        $flashcard->options()->delete();
        $options = [];

        switch ($flashcard->type) {
            case 'boolean':
                $options = [
                    ['text' => 'Tak', 'is_correct' => strtolower($flashcard->answer) === 'tak'],
                    ['text' => 'Nie', 'is_correct' => strtolower($flashcard->answer) === 'nie'],
                ];
                break;

            case 'text':
            default:
                $options[] = ['text' => $flashcard->answer, 'is_correct' => true];

                $gemini = new GeminiService();
                $prompt = "Wygeneruj 3 sensowne, ale błędne odpowiedzi do pytania:{$flashcard->question}. Poprawna odpowiedź: {$flashcard->answer}. Podaj tylko same odpowiedzi, oddzielone średnikami. Odpowiedzi muszą być w podobnej gramatycznie postaci jak poprawna i takim samym formacie. Jeśli pytanie i odpowiedź to to samo słowo, ale w różnych językach, to podaj coś co brzmi podobnie lub losowy termin z tego samego zakresu.";
                $text = $gemini->generate($prompt);

                $others = array_map('trim', explode(';', $text));
                foreach ($others as $answer) {
                    if (!empty($answer)) {
                        $options[] = ['text' => $answer, 'is_correct' => false];
                    }
                }
                break;
        }

        shuffle($options);

        foreach ($options as $opt) {
            FlashcardOption::create([
                'flashcard_id' => $flashcard->id,
                'text' => $opt['text'],
                'is_correct' => $opt['is_correct'],
            ]);
        }
    }
}