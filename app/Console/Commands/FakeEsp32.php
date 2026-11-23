<?php

namespace App\Console\Commands;

use App\Support\ScheduleWindow;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FakeEsp32 extends Command
{
    protected $signature = 'lightsense:fake-esp32
        {key : The light\'s secret key (from the Devices page)}
        {--url=http://127.0.0.1:8000 : Address of the website}
        {--interval=5 : Seconds between measurements}
        {--batch=3 : How many measurements are sent together}
        {--outage=0 : Measurements to hold back, to test offline saving}';

    protected $description = 'Behaves like the real ESP32: downloads settings, decides by itself, saves readings while offline, sends them over HTTP';

    private array $settings = [];
    private bool $presence = false;
    private float $energy = 0;

    public function handle(): int
    {
        $base = rtrim($this->option('url'), '/');
        $key = $this->argument('key');
        $interval = max(1, (int) $this->option('interval'));
        $batch = max(1, (int) $this->option('batch'));
        $outage = max(0, (int) $this->option('outage'));

        $http = fn () => Http::withToken($key)->acceptJson()->timeout(5);

        if (! $this->loadSettings($http, $base)) {
            return self::FAILURE;
        }

        $this->line('Running like an ESP32. Press Ctrl+C to stop.');
        if ($outage > 0) {
            $this->line("After 6 measurements it will pretend to lose Wi-Fi for {$outage} measurements.");
        }

        $buffer = [];
        $tick = 0;

        while (true) {
            $tick++;
            $buffer[] = $this->measure($interval);

            $offline = $outage > 0 && $tick > 6 && $tick <= 6 + $outage;

            if ($offline) {
                $this->warn('  (simulated Wi-Fi outage) holding ' . count($buffer) . ' readings in memory');
            } elseif (count($buffer) >= $batch) {
                $buffer = $this->flush($http, $base, $buffer);
            }

            sleep($interval);
        }
    }

    // GET /api/settings, like the ESP32 does at start-up and when the version changes
    private function loadSettings(callable $http, string $base): bool
    {
        try {
            $res = $http()->get("$base/api/settings");
        } catch (\Throwable $e) {
            $this->error("Cannot reach {$base}. Is `php artisan serve` running?");

            return false;
        }

        if (! $res->successful()) {
            $this->error("Website answered {$res->status()}. Check the key. " . substr($res->body(), 0, 200));

            return false;
        }

        $this->settings = $res->json();
        $this->info(sprintf('Settings loaded: mode=%s, switch on below %d lux, %d schedule(s), version %s',
            $this->settings['mode'], $this->settings['lux_threshold'], count($this->settings['schedules']), $this->settings['version']));

        return true;
    }

    // POST /api/readings with everything saved so far. Anything that fails stays in the buffer.
    private function flush(callable $http, string $base, array $buffer): array
    {
        while ($buffer) {
            $chunk = array_slice($buffer, 0, 100);
            $now = time();

            $payload = ['readings' => array_map(fn ($r) => [
                'lux' => $r['lux'],
                'presence' => $r['presence'],
                'lights_on' => $r['lights_on'],
                'voltage' => $r['voltage'],
                'current' => $r['current'],
                'power' => $r['power'],
                'energy_kwh' => $r['energy_kwh'],
                'age_s' => $now - $r['taken_at'],
            ], $chunk)];

            try {
                $res = $http()->post("$base/api/readings", $payload);
            } catch (\Throwable $e) {
                $this->error('  cannot reach the website, keeping ' . count($buffer) . ' readings');

                return $buffer;
            }

            if (! $res->successful()) {
                $this->error("  website answered {$res->status()}, keeping " . count($buffer) . ' readings');
                $this->line('  ' . substr($res->body(), 0, 200));

                return $buffer;
            }

            $buffer = array_slice($buffer, count($chunk));
            $this->info('  sent ' . count($chunk) . ' readings');

            $version = $res->json('settings_version');
            if ($version && $version !== ($this->settings['version'] ?? null)) {
                $this->line('  settings changed on the website, downloading them');
                $this->loadSettings($http, $base);
            }
        }

        return $buffer;
    }

    // One round of "read the sensors, decide, switch the relay"
    private function measure(int $interval): array
    {
        $now = now();
        $hour = $now->hour + $now->minute / 60;

        // Daylight: dark at night, brightest around noon
        $lux = (int) round(max(0, sin(M_PI * ($hour - 6) / 12)) * 900 + rand(0, 30));

        // People are more often in the room in the morning and evening
        $busy = ($hour >= 6 && $hour < 8) || ($hour >= 17 && $hour < 23);
        $this->presence = $this->presence
            ? rand(1, 100) > ($busy ? 8 : 25)
            : rand(1, 100) <= ($busy ? 30 : 6);

        // The same decision the real firmware makes, using the saved settings
        $s = $this->settings;
        $allowed = ScheduleWindow::allows($s['schedules'] ?? [], $now);

        $lights = ($s['mode'] ?? 'automatic') === 'manual'
            ? (bool) ($s['manual_on'] ?? false)
            : ($this->presence && $lux < ($s['lux_threshold'] ?? 300) && $allowed);

        $voltage = round(228 + rand(-40, 60) / 10, 1);
        $power = $lights ? round(34 + rand(-20, 20) / 10, 1) : 0;
        $this->energy += $power / 1000 * ($interval / 3600);

        $this->line(sprintf('%s  %4d lux  %-8s  %-6s  lights %-3s  %5.1f W',
            $now->format('H:i:s'), $lux, $this->presence ? 'present' : 'empty',
            $s['mode'] ?? 'automatic', $lights ? 'on' : 'off', $power));

        return [
            'taken_at' => time(),
            'lux' => $lux,
            'presence' => $this->presence,
            'lights_on' => $lights,
            'voltage' => $voltage,
            'current' => round($power / $voltage, 3),
            'power' => $power,
            'energy_kwh' => round($this->energy, 4),
        ];
    }
}
