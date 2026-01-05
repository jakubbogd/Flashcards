<?php
namespace App\Services;

use App\Models\StudyDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StreakService
{
    public function markToday(?\DateTimeInterface $date = null): void
    {
        $date = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
        if (!StudyDay::where('date', $date)->where('user_id', Auth::id())->exists()) {
            StudyDay::create(['date' => $date, 'user_id'=> Auth::id()]);
        }
    }

    public function current(): int
    {
        $dates = StudyDay::where('user_id', Auth::id())
            ->orderByDesc('date')
            ->pluck('date')
            ->toArray();

        $streak = 0;
        $cursor = Carbon::today();

        foreach ($dates as $date) {
            $dateCarbon = Carbon::parse($date);

            if ($streak === 0 && $dateCarbon->lt(Carbon::yesterday())) {
                return 0;
            }

            if ($dateCarbon->toDateString() === $cursor->toDateString()) {
                $streak++;
                $cursor->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
