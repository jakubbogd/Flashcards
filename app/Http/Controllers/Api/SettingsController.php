<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function show()
    {
        return response()->json(Settings::firstOrCreate(['user_id' => Auth::id()]),200);
    }

    public function update(UpdateSettingsRequest $request)
    {
        $settings = Settings::firstOrCreate(['user_id' => Auth::id()]);
        $settings->update($request->validated());
        return response()->json($settings);
    }

    public function user()
    {
        return response()->json(Auth::user(),200);
    }   
}
