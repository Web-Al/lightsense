<?php

namespace App\Support;

use App\Models\Device;
use App\Models\Reading;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

// Turns stored readings into numbers and events for the dashboard pages.
class Telemetry
{
    // kWh used per day since $from. Uses the meter's running total: biggest minus smallest value that day.
    public static function energyByDay(int $deviceId, Carbon $from): Collection
    {
        return DB::table('readings')
            ->selectRaw('DATE(recorded_at) as d, MAX(energy_kwh) - MIN(energy_kwh) as kwh')
            ->where('device_id', $deviceId)
            ->where('recorded_at', '>=', $from)
            ->whereNotNull('energy_kwh')
            ->groupBy('d')
            ->pluck('kwh', 'd');
    }

    // Recent things that happened, newest first: [day label, type, text, time]
    public static function events(Device $device, int $limit = 30): array
    {
        $rows = Reading::where('device_id', $device->id)
            ->where('recorded_at', '>=', now()->subDays(3))
            ->orderBy('recorded_at')
            ->get(['recorded_at', 'lights_on', 'lux', 'presence']);

        $events = [];
        $previous = null;

        foreach ($rows as $row) {
            if ($previous) {
                $gap = $row->recorded_at->timestamp - $previous->recorded_at->timestamp;

                if ($gap > 180) {
                    $events[] = [$row->recorded_at, 'system', 'No readings for ' . self::duration($gap) . '. The light reconnected.'];
                }

                if ($row->lights_on !== $previous->lights_on) {
                    $events[] = [$row->recorded_at, 'lights', $row->lights_on
                        ? "Lights turned on at {$row->lux} lux" . ($row->presence ? ': someone is in the room' : '')
                        : 'Lights turned off' . ($row->presence ? '' : ': room is empty')];
                }
            }

            $previous = $row;
        }

        return collect($events)
            ->sortByDesc(fn ($e) => $e[0]->timestamp)
            ->take($limit)
            ->map(fn ($e) => [
                $e[0]->isToday() ? 'Today' : ($e[0]->isYesterday() ? 'Yesterday' : $e[0]->format('M j')),
                $e[1],
                $e[2],
                $e[0]->format('g:i A'),
            ])
            ->values()
            ->all();
    }

    private static function duration(int $seconds): string
    {
        return $seconds >= 3600
            ? round($seconds / 3600, 1) . ' hours'
            : max(1, round($seconds / 60)) . ' minutes';
    }
}
