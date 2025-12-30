<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\StudyDay;
use App\Services\StreakService;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class StreakServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_counts_consecutive_days()
    {
        $service = new StreakService();

        StudyDay::create(['date' => Carbon::today()]);
        StudyDay::create(['date' => Carbon::yesterday()]);

        $this->assertEquals(2, $service->current());
    }

    #[Test]
    public function streak_is_zero_when_today_missing()
    {
        $service = new StreakService();

        StudyDay::create(['date' => Carbon::yesterday()]);

        $this->assertEquals(0, $service->current());
    }

    #[Test]
    public function it_breaks_on_gap()
    {
        $service = new StreakService();

        StudyDay::create(['date' => Carbon::today()]);
        StudyDay::create(['date' => Carbon::today()->subDays(2)]);

        $this->assertEquals(1, $service->current());
    }

    #[Test]
    public function mark_today_creates_only_one_entry()
    {
        $service = new StreakService();

        $service->markToday();
        $service->markToday();

        $this->assertEquals(1, StudyDay::count());
    }

    #[Test]
    public function test_streak_increases_only_once_per_day()
    {
        $service = new StreakService();

        $service->markToday();
        $service->markToday(); // powtórne oznaczenie tego samego dnia

        $streak = $service->current();
        $this->assertEquals(1, $streak); // powinien liczyć tylko jeden dzień
    }

    #[Test]
    public function test_streak_resets_after_missing_day()
    {
        $service = new StreakService();

        StudyDay::create(['date' => now()->subDays(2)->toDateString()]);

        $streak = $service->current();
        $this->assertEquals(0, $streak);
    }


}
