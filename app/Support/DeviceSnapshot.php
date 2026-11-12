<?php

namespace App\Support;

use App\Models\Device;

/**
 * Builds the JSON the dashboard shows for one device. The same array is used
 * for the first page render and for every 2-second poll, so they never disagree.
 */
class DeviceSnapshot
{
    public static function for(Device $device): array
    {
        $latest = $device->readings()->latest('recorded_at')->first();

        $lastMovement = $device->readings()
            ->where('presence', true)
            ->latest('recorded_at')
            ->value('recorded_at');

        // The power meter reports a running total, so "today" and "this month"
        // are the latest total minus the first total recorded in that period.
        $monthStartWh = $device->readings()
            ->where('recorded_at', '>=', now()->startOfMonth())
            ->oldest('recorded_at')
            ->value('energy_wh');
        $dayStartWh = $device->readings()
            ->where('recorded_at', '>=', now()->startOfDay())
            ->oldest('recorded_at')
            ->value('energy_wh');

        $latestWh = $latest ? (float) $latest->energy_wh : 0.0;
        $todayKwh = $dayStartWh !== null ? max(0, $latestWh - (float) $dayStartWh) / 1000 : 0;
        $monthKwh = $monthStartWh !== null ? max(0, $latestWh - (float) $monthStartWh) / 1000 : 0;

        $series = [];
        if ($latest) {
            $series = $device->readings()
                ->where('recorded_at', '>=', $latest->recorded_at->copy()->subSeconds(60))
                ->oldest('recorded_at')
                ->pluck('power')
                ->map(fn ($watts) => (float) $watts)
                ->all();
        }

        $online = $device->isOnline();
        $lux = $latest?->lux;

        return [
            'online' => $online,
            'statusText' => $online
                ? 'Sensors online, updating every 2 seconds'
                : ($latest
                    ? 'Sensors offline, last reading '.$latest->recorded_at->diffForHumans()
                    : 'Waiting for the first sensor reading'),

            'mode' => $device->mode,
            'modeHelp' => $device->mode === 'auto'
                ? 'Lights switch on when someone is in the room and it is dark.'
                : 'Lights stay exactly as you set them. Switch to Automatic to use the sensors.',
            'luxThreshold' => $device->lux_threshold,

            'lightsOn' => $device->lights_on,
            'lightsLabel' => $device->lights_on ? 'On' : 'Off',
            'lightsButton' => $device->lights_on ? 'Turn off' : 'Turn on',

            'presence' => $latest?->presence,
            'presenceLabel' => $latest ? ($latest->presence ? 'Occupied' : 'Empty') : 'No data',
            'lastMovement' => $lastMovement
                ? 'Last movement: '.$lastMovement->diffForHumans()
                : 'Last movement: none yet',

            'lux' => $lux !== null ? (string) $lux : '–',
            'luxPct' => $lux !== null ? min(100, round($lux / 1000 * 100)) : 0,

            'power' => number_format((float) ($latest->power ?? 0), 0),
            'voltage' => number_format((float) ($latest->voltage ?? 0), 0),
            'current' => number_format((float) ($latest->current ?? 0), 2),
            'todayKwh' => number_format($todayKwh, 2),
            'monthKwh' => number_format($monthKwh, 1),

            'series' => $series,

            'activity' => $device->activities()
                ->orderByDesc('id')
                ->limit(5)
                ->get()
                ->map(fn ($entry) => [
                    'message' => $entry->message,
                    'time' => $entry->created_at->format('h:i A'),
                ])
                ->all(),
        ];
    }
}
