<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Reading;
use App\Models\Schedule;
use App\Support\Telemetry;

class DashboardController extends Controller
{
    // Which light are we looking at? (chosen with ?device=ID, default: the first one)
    private function pickDevice(): array
    {
        $devices = Device::orderBy('location')->get();
        $device = $devices->firstWhere('id', (int) request('device')) ?? $devices->first();

        return [$devices, $device];
    }

    public function overview()
    {
        [$devices, $device] = $this->pickDevice();
        $latest = $device?->latestReading;

        $byDay = $device ? Telemetry::energyByDay($device->id, now()->startOfMonth()) : collect();

        $lastMovement = $device
            ? Reading::where('device_id', $device->id)->where('presence', true)->latest('recorded_at')->first(['recorded_at'])?->recorded_at
            : null;

        $room = [
            'name' => $device?->location ?? 'No light added yet',
            'hasData' => $latest !== null,
            'lights_on' => (bool) ($latest?->lights_on),
            'presence' => $latest ? ($latest->presence ? 'Occupied' : 'Empty') : 'No data',
            'last_movement' => $lastMovement ? $lastMovement->diffForHumans() : 'none yet',
            'brightness_lux' => $latest?->lux ?? 0,
            'brightness_pct' => min(100, (int) round(($latest?->lux ?? 0) / 10)),
            'power_w' => round((float) ($latest?->power ?? 0), 1),
            'today_kwh' => round((float) ($byDay[now()->toDateString()] ?? 0), 2),
            'voltage' => round((float) ($latest?->voltage ?? 0)),
            'current' => (float) ($latest?->current ?? 0),
            'month_kwh' => round((float) $byDay->sum(), 1),
            'mode' => $device?->mode ?? 'automatic',
            'manual_on' => (bool) ($device?->manual_on),
            'threshold_lux' => $device?->lux_threshold ?? 300,
        ];

        // Power chart: the latest 24 readings drawn as a line
        $powers = $device
            ? Reading::where('device_id', $device->id)->latest('recorded_at')->take(24)->pluck('power')->reverse()->values()
            : collect();
        $chartPoints = '0,106 400,106';
        if ($powers->count() > 1) {
            $max = max(10, (float) $powers->max());
            $step = 400 / ($powers->count() - 1);
            $chartPoints = $powers->map(fn ($p, $i) => round($i * $step, 1) . ',' . round(106 - ((float) $p / $max) * 96, 1))->implode(' ');
        }

        $schedules = Schedule::orderBy('start_time')->take(3)->get();

        $activity = $device
            ? collect(Telemetry::events($device, 5))->map(fn ($e) => [$e[2], $e[3]])->all()
            : [];

        if (! $device) {
            $sub = 'Ask an administrator to add a light on the Devices page';
        } else {
            $sub = collect([
                $device->area,
                $device->last_seen_at ? 'Last reading ' . $device->last_seen_at->diffForHumans() : 'No readings yet',
            ])->filter()->join('. ');
        }

        return view('dashboard', compact('room', 'schedules', 'activity', 'device', 'devices', 'sub', 'chartPoints'));
    }

    public function energy()
    {
        [, $device] = $this->pickDevice();
        $rate = 12.00; // sample price in pesos per kWh - change to your own rate

        $byDay = $device
            ? Telemetry::energyByDay($device->id, now()->startOfMonth()->min(now()->subDays(6)->startOfDay()))
            : collect();

        $week = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $week[] = [$date->format('D'), round((float) ($byDay[$date->toDateString()] ?? 0), 2)];
        }

        $monthStart = now()->startOfMonth()->toDateString();
        $monthKwh = round((float) $byDay->filter(fn ($v, $day) => $day >= $monthStart)->sum(), 1);

        $latest = $device?->latestReading;
        $live = [
            'voltage' => round((float) ($latest?->voltage ?? 0)),
            'current' => (float) ($latest?->current ?? 0),
            'power' => round((float) ($latest?->power ?? 0), 1),
            'power_factor' => '-', // the PZEM can report it; the website does not store it yet
        ];

        return view('dashboard.energy', [
            'week' => $week,
            'live' => $live,
            'todayKwh' => round((float) ($byDay[now()->toDateString()] ?? 0), 2),
            'monthKwh' => $monthKwh,
            'avgKwh' => round(collect($week)->avg(1), 2),
            'rate' => $rate,
            'monthCost' => $monthKwh * $rate,
            'sub' => $device ? "Power use for {$device->location}" : 'No light added yet',
        ]);
    }

    public function activity()
    {
        [, $device] = $this->pickDevice();

        $events = $device ? Telemetry::events($device, 40) : [];
        $sub = $device ? "Everything that happened in {$device->location}" : 'No light added yet';

        return view('dashboard.activity', compact('events', 'sub'));
    }
}
