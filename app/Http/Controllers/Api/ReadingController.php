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
            'server_time' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    // The ESP32 sends one reading, or a batch of up to 100 saved while it was offline.
    // "age_s" says how many seconds ago the reading was taken, so the ESP32's clock does not matter.
    public function store(Request $request)
    {
        $device = $request->attributes->get('device');

        $item = [
            'lux' => ['required', 'integer', 'min:0', 'max:100000'],
            'presence' => ['required', 'boolean'],
            'lights_on' => ['nullable', 'boolean'],
            'voltage' => ['nullable', 'numeric', 'between:0,500'],
            'current' => ['nullable', 'numeric', 'between:0,100'],
            'power' => ['nullable', 'numeric', 'between:0,30000'],
            'energy_kwh' => ['nullable', 'numeric', 'min:0'],
            'age_s' => ['nullable', 'integer', 'min:0', 'max:604800'],
        ];

        if ($request->has('readings')) {
            $rules = ['readings' => ['required', 'array', 'min:1', 'max:100']];
            foreach ($item as $field => $rule) {
                $rules["readings.*.$field"] = $rule;
            }
            $items = $request->validate($rules)['readings'];
        } else {
            $items = [$request->validate($item)];
        }

        $now = now();
        $rows = array_map(fn ($r) => [
            'device_id' => $device->id,
            'lux' => $r['lux'],
            'presence' => $r['presence'],
            'lights_on' => $r['lights_on'] ?? false,
            'voltage' => $r['voltage'] ?? null,
            'current' => $r['current'] ?? null,
            'power' => $r['power'] ?? null,
            'energy_kwh' => $r['energy_kwh'] ?? null,
            'recorded_at' => $now->copy()->subSeconds($r['age_s'] ?? 0),
            'created_at' => $now,
            'updated_at' => $now,
        ], $items);

        Reading::insert($rows);
        $device->update(['last_seen_at' => $now]);

        // Tell the device whether its settings are out of date
        return response()->json([
            'ok' => true,
            'saved' => count($rows),
            'settings_version' => $device->settingsVersion(),
        ], 201);
    }
}
