<?php
// tests/Feature/SettingsTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Settings;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_it_returns_settings()
    {
        Settings::firstOrCreate([]);

        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'dark_mode',
                     'created_at',
                     'updated_at',
                 ]);
    }

    #[Test]
    public function test_it_updates_dark_mode()
    {
        $response = $this->putJson('/api/settings', [
            'dark_mode' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'dark_mode' => true,
            ]);

        $this->assertDatabaseHas('settings', [
            'dark_mode' => true,
        ]);
    }

    #[Test]
    public function test_dark_mode_is_required()
    {
        $response = $this->putJson('/api/settings', []);

        $response->assertStatus(422);
    }
}
