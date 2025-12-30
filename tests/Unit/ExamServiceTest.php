<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ExamService;

class ExamServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_easy_config()
    {
        $service = new ExamService();

        $config = $service->config('easy');

        $this->assertEquals(5, $config['count']);
        $this->assertEquals(600, $config['time']);
    }

    #[Test]
    public function test_normal_config()
    {
        $service = new ExamService();

        $config = $service->config('normal');

        $this->assertEquals(10, $config['count']);
        $this->assertEquals(1200, $config['time']);
    }

    #[Test]
    public function test_hard_config()
    {
        $service = new ExamService();

        $config = $service->config('hard');

        $this->assertEquals(20, $config['count']);
        $this->assertEquals(1800, $config['time']);
    }
}
