<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Settings;

class SettingsController extends Controller
{
    public function show()
    {
        return response()->json(Settings::firstOrCreate([]),200);
    }

    public function update(UpdateSettingsRequest $request)
    {
        $settings = Settings::firstOrCreate([]);
        $settings->update($request->validated());
        return response()->json($settings);
    }
}
