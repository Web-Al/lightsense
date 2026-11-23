@php $max = max(array_column($week, 1)) ?: 1; @endphp
<x-layouts.dashboard title="Energy" heading="Energy" :sub="$sub">

    <section class="mt-4 grid grid-cols-2 gap-px border border-line bg-line lg:grid-cols-4">
        @foreach ([
            ['today', number_format($todayKwh, 2), 'kWh'],
            ['this month', number_format($monthKwh, 1), 'kWh'],
            ['daily average', number_format($avgKwh, 2), 'kWh'],
            ['estimated cost', '₱'.number_format($monthCost, 2), 'this month'],
        ] as [$label, $value, $unit])
            <div class="bg-panel p-3">
                <p class="metric text-[10px] text-stone-600">{{ $label }}</p>
                <p class="metric mt-1 break-words text-xl font-medium sm:text-2xl">{{ $value }} <span class="text-[11px] font-normal text-stone-500">{{ $unit }}</span></p>
            </div>
        @endforeach
    </section>
    <p class="metric mt-2 text-[10px] text-stone-500">Cost is estimated at ₱{{ number_format($rate, 2) }} per kWh. Change the rate in DashboardController.</p>

    <section class="mt-3 grid gap-3 lg:grid-cols-[1.7fr_1fr]">
        <div class="border border-line bg-panel p-4">
            <h2 class="text-[11px] font-semibold">Energy used per day</h2>
            <div class="mt-4 flex h-44 items-end gap-2 sm:gap-4" role="img" aria-label="Bar chart of kilowatt-hours used in the last 7 days">
                @foreach ($week as [$day, $kwh])
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-1.5">
                        <span class="metric text-[10px] {{ $loop->last ? 'text-live' : 'text-stone-500' }}">{{ number_format($kwh, 1) }}</span>
                        {{-- Past days are muted. Only today gets the bright amber. --}}
                        <div class="w-full rounded-t-sm bg-gradient-to-t {{ $loop->last ? 'from-flame/30 via-brass to-live' : 'from-brass/10 to-brass/50' }}" style="height: {{ round(($kwh / $max) * 100) }}%"></div>
                        <span class="metric text-[10px] {{ $loop->last ? 'font-semibold text-stone-900' : 'text-stone-500' }}">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
            <p class="mt-3 text-[10px] text-stone-500">Kilowatt-hours (kWh) used each day. Today is the bright bar.</p>
        </div>

        <div class="border border-line bg-panel p-4">
            <h2 class="flex items-center gap-1.5 text-[11px] font-semibold">Live readings <span class="size-1.5 rounded-full bg-live pulse-live"></span></h2>
            <dl class="mt-3 divide-y divide-line text-[11px]">
                @foreach ([
                    ['voltage', $live['voltage'].' V'],
                    ['current', number_format($live['current'], 2).' A'],
                    ['power', $live['power'].' W'],
                    ['power factor', $live['power_factor']],
                ] as [$k, $v])
                    <div class="flex justify-between py-2"><dt class="metric text-stone-600">{{ $k }}</dt><dd class="metric font-medium">{{ $v }}</dd></div>
                @endforeach
            </dl>
            <p class="mt-2 text-[10px] text-stone-500">From the PZEM-004T meter once your device is connected.</p>
        </div>
    </section>
</x-layouts.dashboard>
