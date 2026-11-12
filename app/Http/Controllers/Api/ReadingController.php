<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    // Lets a device check that its key works
    public function ping(Request $request)
    {
        $device = $request->attributes->get('device');

        return response()->json([
            'ok' => true,
            'device' => $device->name,
            'location' => $device->location,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    // The ESP32 sends its sensor values here
    public function store(Request $request)
    {
        $device = $request->attributes->get('device');

        $data = $request->validate([
            'lux' => ['required', 'integer', 'min:0', 'max:100000'],
            'presence' => ['required', 'boolean'],
            'lights_on' => ['nullable', 'boolean'],
            'voltage' => ['nullable', 'numeric', 'between:0,500'],
            'current' => ['nullable', 'numeric', 'between:0,100'],
            'power' => ['nullable', 'numeric', 'between:0,30000'],
            'energy_kwh' => ['nullable', 'numeric', 'min:0'],
        ]);

        $device->readings()->create($data + ['recorded_at' => now()]);
        $device->update(['last_seen_at' => now()]);

        return response()->json(['ok' => true], 201);
    }
}
