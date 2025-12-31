<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\MotivationService;
use PHPUnit\Framework\Attributes\Test;


class MotivationServiceTest extends TestCase
{
    #[Test]
    public function it_returns_great_message_for_high_score()
    {
        $service = new MotivationService();

        $message = $service->message(90);

        $this->assertIsString($message);
    }

    #[Test]
    public function it_returns_mid_message_for_30_50()
    {
        $service = new MotivationService();

        $message = $service->message(40);

        $this->assertIsString($message);
    }

    #[Test]
    public function it_returns_bad_message_for_low_score()
    {
        $service = new MotivationService();

        $message = $service->message(10);

        $this->assertIsString($message);
    }
}
