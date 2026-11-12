<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Reading;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Demo account:  demo@lightsense.test / password
     * Matches the Figma dashboard: Living room, lights off, room empty, 420 lux.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@lightsense.test'],
            ['name' => 'Juan Dela Cruz', 'password' => 'password'],
        );

        if ($user->devices()->exists()) {
            return;
        }

        $device = $user->devices()->create([
            'name' => 'Living room',
            'mode' => 'auto',
            'lights_on' => false,
            'lux_threshold' => 300,
            'last_seen_at' => now(),
        ]);

        $device->schedules()->createMany([
            ['name' => 'Evening lights', 'days' => 'daily',    'starts_at' => '18:00', 'ends_at' => '23:00', 'enabled' => true],
            ['name' => 'Early morning',  'days' => 'weekdays', 'starts_at' => '05:30', 'ends_at' => '07:00', 'enabled' => true],
            ['name' => 'Night off',      'days' => 'daily',    'starts_at' => '23:00', 'ends_at' => null,    'enabled' => false],
        ]);

        // The meter reports a running total in Wh. These totals give
        // "38.6 kWh this month" and "1.84 kWh today" on the dashboard.
        $latestWh = 138600;
        $dayStartWh = 136760;
        $monthStartWh = 100000;

        $rows = [];

        if (! now()->isSameDay(now()->startOfMonth())) {
            $rows[] = $this->reading($device->id, now()->startOfMonth()->addMinute(), $monthStartWh, 0, false);
        }
        $rows[] = $this->reading($device->id, now()->startOfDay()->addMinute(), $dayStartWh, 0, false);

        // Last two minutes: lights were on while someone was in the room, then they left.
        for ($i = 0; $i < 60; $i++) {
            $on = $i < 42;
            $rows[] = $this->reading(
                $device->id,
                now()->subSeconds((59 - $i) * 2),
                $on ? round($latestWh - (41 - $i) * 0.03, 2) : $latestWh,
                $on ? 58 + ($i % 3) * 0.4 : 0,
                $on,
            );
        }

        Reading::insert($rows);

        ActivityLog::insert([
            ['device_id' => $device->id, 'message' => 'Lights turned off automatically', 'created_at' => now()->subSeconds(40), 'updated_at' => now()->subSeconds(40)],
            ['device_id' => $device->id, 'message' => 'Dashboard connected',             'created_at' => now(),                  'updated_at' => now()],
        ]);
    }

    private function reading(int $deviceId, $at, float $energyWh, float $power, bool $presence): array
    {
        $voltage = 230.0;

        return [
            'device_id' => $deviceId,
            'presence' => $presence,
            'lux' => $presence ? 280 : 420,
            'voltage' => $voltage,
            'current' => round($power / $voltage, 3),
            'power' => $power,
            'energy_wh' => $energyWh,
            'recorded_at' => $at->format('Y-m-d H:i:s'),
        ];
    }
}
