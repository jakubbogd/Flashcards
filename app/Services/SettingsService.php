<?php

namespace App\Services;

use App\Models\Settings;

class SettingsService
{
    public function get(): Settings
    {
        return Settings::firstOrCreate([]);
    }

    public function update(array $data): Settings
    {
        $settings = $this->get();
        $settings->update($data);

        return $settings;
    }
}
