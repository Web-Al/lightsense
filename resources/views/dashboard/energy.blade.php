@php $max = max(array_column($week, 1)); @endphp
<x-layouts.dashboard title="Energy" heading="Energy" sub="Power use for the Living room (sample data)">

    <section class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            ['Today', number_format($todayKwh, 2), 'kWh'],
            ['This month', number_format($monthKwh, 1), 'kWh'],
            ['Daily average', number_format($avgKwh, 2), 'kWh'],
            ['Estimated cost', '₱'.number_format($monthCost, 2), 'this month'],
        ] as [$label, $value, $unit])
            <div class="rounded-lg border border-line bg-panel-2 p-3">
                <p class="text-[10px] text-neutral-500">{{ $label }}</p>
                <p class="mt-1 text-xl font-bold">{{ $value }} <span class="text-[10px] font-normal text-neutral-500">{{ $unit }}</span></p>
            </div>
        @endforeach
    </section>
    <p class="mt-2 text-[10px] text-neutral-500">Cost is estimated at ₱{{ number_format($rate, 2) }} per kWh. Change the rate in DashboardController.</p>

    <section class="mt-3 grid gap-3 lg:grid-cols-[1.7fr_1fr]">
        <div class="rounded-lg border border-line bg-panel-2 p-4">
            <h2 class="text-[11px] font-semibold">Energy used per day</h2>
            <div class="mt-4 flex h-44 items-end gap-2 sm:gap-4" role="img" aria-label="Bar chart of kilowatt-hours used in the last 7 days">
                @foreach ($week as [$day, $kwh])
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-1.5">
                        <span class="text-[10px] text-neutral-400">{{ number_format($kwh, 1) }}</span>
                        <div class="w-full rounded-t bg-amber {{ $loop->last ? '' : 'opacity-60' }}" style="height: {{ round(($kwh / $max) * 100) }}%"></div>
                        <span class="text-[10px] {{ $loop->last ? 'font-semibold text-white' : 'text-neutral-500' }}">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
            <p class="mt-3 text-[10px] text-neutral-500">Kilowatt-hours (kWh) used each day. Today is the bright bar.</p>
        </div>

        <div class="rounded-lg border border-line bg-panel-2 p-4">
            <h2 class="text-[11px] font-semibold">Live readings</h2>
            <dl class="mt-3 divide-y divide-line text-[11px]">
                @foreach ([
                    ['Voltage', $live['voltage'].' V'],
                    ['Current', number_format($live['current'], 2).' A'],
                    ['Power', $live['power'].' W'],
                    ['Power factor', $live['power_factor']],
                ] as [$k, $v])
                    <div class="flex justify-between py-2"><dt class="text-neutral-400">{{ $k }}</dt><dd class="font-semibold">{{ $v }}</dd></div>
                @endforeach
            </dl>
            <p class="mt-2 text-[10px] text-neutral-500">From the PZEM-004T meter once your device is connected.</p>
        </div>
    </section>
</x-layouts.dashboard>
