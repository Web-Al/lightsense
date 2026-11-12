<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Schedule;

class DashboardController extends Controller
{
    // NOTE: all numbers below are sample data. Replace them with readings
    // from your ESP32 once the readings table exists.

    public function overview()
    {
        // Which light are we looking at? (chosen with ?device=ID, default: the first one)
        $devices = Device::orderBy('location')->get();
        $device = $devices->firstWhere('id', (int) request('device')) ?? $devices->first();

        $room = [
            'name' => $device?->location ?? 'No light added yet',
            'lights_on' => false,
            'presence' => 'Empty',
            'brightness_lux' => 420,
            'brightness_pct' => 55,
            'power_w' => 0,
            'today_kwh' => 1.84,
            'voltage' => 230,
            'current' => 0.00,
            'month_kwh' => 38.6,
            'threshold_lux' => 300,
        ];

        $schedules = Schedule::orderBy('start_time')->take(3)->get();

        $activity = [
            ['Dashboard connected', '04:05 PM'],
        ];

        $sub = $device
            ? collect([$device->area, $device->last_seen_at ? 'Last reading ' . $device->last_seen_at->diffForHumans() : 'No readings yet'])->filter()->join('. ')
            : 'Ask an administrator to add a light on the Devices page';

        return view('dashboard', compact('room', 'schedules', 'activity', 'device', 'devices', 'sub'));
    }

    public function energy()
    {
        $rate = 12.00; // sample price in pesos per kWh - change to your own rate

        $week = [ // kWh used per day, last 7 days
            ['Fri', 1.20], ['Sat', 1.90], ['Sun', 2.10], ['Mon', 1.60],
            ['Tue', 1.40], ['Wed', 2.00], ['Thu', 1.84],
        ];

        $live = ['voltage' => 230, 'current' => 0.00, 'power' => 0, 'power_factor' => 0.98];
        $monthKwh = 38.6;

        return view('dashboard.energy', [
            'week' => $week,
            'live' => $live,
            'todayKwh' => 1.84,
            'monthKwh' => $monthKwh,
            'avgKwh' => round(collect($week)->avg(1), 2),
            'rate' => $rate,
            'monthCost' => $monthKwh * $rate,
        ]);
    }

    public function activity()
    {
        // [day, type (lights | schedule | system), text, time]
        $events = [
            ['Today', 'system', 'Dashboard connected', '04:05 PM'],
            ['Today', 'lights', 'Lights turned off: room is empty', '03:52 PM'],
            ['Today', 'lights', 'Lights turned on: movement detected, 280 lux', '03:20 PM'],
            ['Today', 'schedule', 'Early morning schedule finished', '07:00 AM'],
            ['Today', 'schedule', 'Early morning schedule started', '05:30 AM'],
            ['Yesterday', 'schedule', 'Night off schedule started', '11:00 PM'],
            ['Yesterday', 'lights', 'Lights turned off by Evening lights schedule', '11:00 PM'],
            ['Yesterday', 'system', 'Sensors reconnected after Wi-Fi drop', '09:14 PM'],
        ];

        return view('dashboard.activity', compact('events'));
    }
}
