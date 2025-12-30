<?php

namespace App\Services;

use Illuminate\Support\Arr;

class MotivationService
{
    protected array $great = [
        '🔥 Rewelacja! Widać solidne przygotowanie.',
        '🏆 Świetny wynik. Tak trzymaj!',
        '🚀 Perfekcyjnie! Twoja praca naprawdę procentuje.',
        '🌟 Doskonała robota! Twój wysiłek się opłaca.',
        '💯 Fantastycznie! Wyraźnie widać progres.',
    ];

    protected array $ok = [
        '👏 Dobra robota. Jesteś na dobrej drodze.',
        '📈 Każda sesja przybliża Cię do celu.',
        '🙂 Widzisz postęp! Kontynuuj naukę.',
        '👍 Solidny wynik. Z każdym razem lepiej.',
        '💡 Nauka idzie w dobrym kierunku — keep it up!',
    ];

    protected array $meh = [
        '🤔 Wynik w porządku, ale jest miejsce na poprawę.',
        '💪 Nie jest źle, ale spróbuj następnym razem lepiej.',
        '🧠 Każdy punkt to krok w dobrą stronę, kontynuuj!',
        '🌱 Lekki progres dzisiaj — jutro może być mocniej!',
        '🙂 Wynik średni, ale ważne, że działasz!',
    ];

    protected array $bad = [
        '💪 To był trening. Wracaj jutro.',
        '🧠 Nauka to proces. Najważniejsze, że działasz.',
        '😌 Nie przejmuj się. Każda próba się liczy.',
        '🌱 Mały krok dzisiaj, wielki postęp jutro.',
        '🔥 Ważne, że próbujesz — z czasem będzie lepiej!',
    ];

    public function exam(int $percent): string
    {
        return match (true) {
            $percent >= 80 => Arr::random($this->great),
            $percent >= 50 => Arr::random($this->ok),
            $percent >= 30 => Arr::random($this->meh),
            default => Arr::random($this->bad),
        };
    }
}
