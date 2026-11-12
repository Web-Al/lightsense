<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Reading;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SimulateReadings extends Command
{
    protected $signature = 'lightsense:simulate
        {device? : ID of the device (default: the first one)}
        {--hours=24 : Hours of past readings to create}
        {--fresh : Delete the device\'s old readings first}
        {--live : Keep adding a new reading every 2 seconds (stop with Ctrl+C)}';

    protected $description = 'Add fake sensor readings so the dashboard has data before you own an ESP32';

    private bool $presence = false;
    private float $energy = 0;

    public function handle(): int
    {
        $device = $this->argument('device') ? Device::find($this->argument('device')) : Device::first();

        if (! $device) {
            $key = Device::newKey();
            $device = Device::create([
                'name' => 'Ceiling light',
                'location' => 'Living room',
                'api_key_hash' => Device::hashKey($key),
            ]);
            $this->info("No device existed, so a demo device was created: {$device->name} ({$device->location}).");
            $this->line("Its key (shown once): {$key}");
        }

        if ($this->option('fresh')) {
            $device->readings()->delete();
        }

        // Past readings: one every 5 minutes
        $hours = max(1, (int) $this->option('hours'));
        $time = now()->subHours($hours);
        $rows = [];

        while ($time->lt(now())) {
            $rows[] = $this->makeRow($device, $time, 5 * 60);
            $time = $time->copy()->addMinutes(5);
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            Reading::insert($chunk);
        }

        $device->update(['last_seen_at' => now()]);
        $this->info(count($rows) . " readings added for {$device->name} ({$device->location}).");

        // Live mode: a new reading every 2 seconds
        if ($this->option('live')) {
            $this->line('Sending live readings. Press Ctrl+C to stop.');

            while (true) {
                $row = $this->makeRow($device, now(), 2);
                Reading::insert([$row]);
                $device->update(['last_seen_at' => now()]);
                $this->line(sprintf('%s  %4d lux  %-8s  lights %-3s  %5.1f W',
                    now()->format('H:i:s'), $row['lux'],
                    $row['presence'] ? 'present' : 'empty',
                    $row['lights_on'] ? 'on' : 'off', $row['power']));
                sleep(2);
            }
        }

        return self::SUCCESS;
    }

    // Builds one believable reading for the given moment
    private function makeRow(Device $device, Carbon $time, int $seconds): array
    {
        $hour = $time->hour + $time->minute / 60;

        // Daylight: dark at night, brightest around noon
        $daylight = max(0, sin(M_PI * ($hour - 6) / 12));
        $lux = (int) round($daylight * 900 + rand(0, 30));

        // People are more often in the room in the morning and evening
        $busy = ($hour >= 6 && $hour < 8) || ($hour >= 17 && $hour < 23);
        if ($this->presence) {
            $this->presence = rand(1, 100) > ($busy ? 10 : 35);
        } else {
            $this->presence = rand(1, 100) <= ($busy ? 25 : 4);
        }

        $lightsOn = $this->presence && $lux < 300;
        $voltage = round(228 + rand(-40, 60) / 10, 2);
        $power = $lightsOn ? round(34 + rand(-20, 20) / 10, 2) : 0;
        $current = round($power / $voltage, 3);
        $this->energy += $power / 1000 * ($seconds / 3600);

        return [
            'device_id' => $device->id,
            'lux' => $lux,
            'presence' => $this->presence,
            'lights_on' => $lightsOn,
            'voltage' => $voltage,
            'current' => $current,
            'power' => $power,
            'energy_kwh' => round($this->energy, 3),
            'recorded_at' => $time->copy(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
