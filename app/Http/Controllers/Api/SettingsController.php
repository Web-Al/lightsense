<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // The ESP32 downloads this, saves it in its own memory, and works from it even when offline.
    public function show(Request $request)
    {
        $device = $request->attributes->get('device');

        return response()->json($device->settingsPayload() + [
            'version' => $device->settingsVersion(),
            'server_time' => now()->format('Y-m-d H:i:s'), // local time, to set the clock
            'timezone' => config('app.timezone'),
        ]);
    }
}
