@php
    $canControl = auth()->user()->isStaff();
    $controlsOn = $canControl && $device;
    $online = $device?->isOnline() ?? false;
    $on = $room['lights_on'];
    $settingsUrl = $device ? route('devices.settings', $device) : '#';
@endphp
<x-layouts.dashboard :map="true" title="Overview" :heading="$room['name']" :sub="$sub" :live="$online">
    <x-slot:actions>
        @if ($devices->count() > 1)
            <form method="GET" action="{{ route('dashboard') }}">
                <select name="device" onchange="this.form.submit()" class="metric w-full max-w-full rounded-sm border border-line bg-panel-2 px-2 py-2 text-[11px] text-stone-700 sm:w-auto" aria-label="Choose a light">
                    @foreach ($devices as $d)
                        <option value="{{ $d->id }}" @selected($device && $d->id === $device->id)>{{ $d->location }} - {{ $d->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </x-slot:actions>

    @if ($device && $room['hasData'] && ! $online)
        <div data-toast-request data-toast-type="warning" data-toast-title="Light is offline" data-toast-message="Showing its last reading. It keeps working on its own and sends saved readings when it reconnects." data-toast-duration="0" hidden></div>
    @endif

    {{-- Readouts: one panel split by 1px lines, not four separate cards --}}
    <section class="mt-4 grid grid-cols-2 gap-px border border-line bg-line lg:grid-cols-4">
        <div id="light-cell" class="bg-panel p-3 {{ $on ? 'glow-live' : '' }}">
            <p class="metric text-[10px] text-stone-600">lights</p>
            <p class="metric mt-1 break-words text-xl font-medium sm:text-2xl {{ $on ? 'text-live' : 'text-stone-900/50' }}">{{ $room['hasData'] ? ($on ? 'On' : 'Off') : '--' }}</p>
            @if ($controlsOn)
                <form method="POST" action="{{ $settingsUrl }}" class="mt-2">
                    @csrf @method('PATCH')
                    <input type="hidden" name="mode" value="manual">
                    <input type="hidden" name="manual_on" value="{{ $on ? 0 : 1 }}">
                    <button class="btn btn-ghost !px-2.5 !py-1 !text-[11px]">{{ $on ? 'Turn off' : 'Turn on' }}</button>
                </form>
            @endif
        </div>

        <div class="bg-panel p-3">
            <p class="metric text-[10px] text-stone-600">presence</p>
            <p class="metric mt-1 break-words text-xl font-medium sm:text-2xl">{{ $room['presence'] }}</p>
            <p class="metric mt-2 text-[10px] text-stone-500">last movement: {{ $room['last_movement'] }}</p>
        </div>

        <div class="bg-panel p-3">
            <p class="metric text-[10px] text-stone-600">room brightness</p>
            <p class="metric mt-1 break-words text-xl font-medium sm:text-2xl">{{ $room['brightness_lux'] }}<span class="ml-1 text-[11px] font-normal text-stone-500">lux</span></p>
            <div class="mt-3 h-1 bg-stone-900/10"><div class="h-1 bg-gradient-to-r from-flame/40 to-live" style="width: {{ $room['brightness_pct'] }}%"></div></div>
        </div>

        <div class="bg-panel p-3">
            <p class="metric flex items-center gap-1.5 text-[10px] text-stone-600">
                power now
                <span class="size-1.5 rounded-full {{ $online ? 'bg-live pulse-live' : 'bg-stone-900/20' }}"></span>
            </p>
            <p class="metric mt-1 break-words text-xl font-medium sm:text-2xl {{ $online ? 'text-live pulse-live' : 'text-stone-900/50' }}">{{ $room['power_w'] }}<span class="ml-1 text-[11px] font-normal text-stone-500">W</span></p>
            <p class="metric mt-2 text-[10px] text-stone-500">today: <span class="text-stone-700">{{ $room['today_kwh'] }} kWh</span></p>
        </div>
    </section>

    {{-- Power use + light control --}}
    <section class="mt-3 grid gap-3 lg:grid-cols-[1.7fr_1fr]">
        <div class="border border-line bg-panel p-4">
            <div class="flex flex-wrap items-center justify-between gap-2 text-[11px]"><h2 class="font-semibold">Power use</h2><span class="metric text-stone-500">latest readings</span></div>
            <svg viewBox="0 0 400 120" class="mt-3 h-36 w-full {{ $online ? 'text-live' : 'text-stone-500' }}" preserveAspectRatio="none" role="img" aria-label="Power use chart">
                @foreach ([10, 34, 58, 82, 106] as $y)
                    <line x1="0" x2="400" y1="{{ $y }}" y2="{{ $y }}" stroke="#1c1917" stroke-opacity="0.09" stroke-width="1"/>
                @endforeach
                <polyline fill="none" stroke="currentColor" stroke-width="2" points="{{ $chartPoints }}"/>
            </svg>
            <div class="mt-3 grid grid-cols-1 gap-px border border-line bg-line sm:grid-cols-3">
                @foreach ([['voltage', $room['voltage'].' V'], ['current', number_format($room['current'], 2).' A'], ['energy this month', $room['month_kwh'].' kWh']] as [$k, $v])
                    <div class="bg-panel p-2"><p class="metric text-[9px] text-stone-500">{{ $k }}</p><p class="metric text-xs font-medium">{{ $v }}</p></div>
                @endforeach
            </div>
        </div>

        <div class="border border-line bg-panel p-4">
            <h2 class="text-[11px] font-semibold">Light control</h2>

            <form method="POST" action="{{ $settingsUrl }}" class="mt-3 grid grid-cols-2 gap-px border border-line bg-line text-[11px]">
                @csrf @method('PATCH')
                @foreach (['automatic' => 'Automatic', 'manual' => 'Manual'] as $value => $label)
                    <button name="mode" value="{{ $value }}" @disabled(! $controlsOn)
                        class="py-1.5 disabled:cursor-not-allowed {{ $room['mode'] === $value ? 'bg-stone-900/10 font-semibold text-stone-900' : 'bg-panel text-stone-600' }}">{{ $label }}</button>
                @endforeach
            </form>
            <p class="mt-3 text-[10px] leading-relaxed text-stone-500">
                {{ $room['mode'] === 'automatic' ? 'Lights switch on when someone is in the room and it is dark.' : 'Manual mode: the light follows the Turn on and Turn off button.' }}
            </p>

            <form method="POST" action="{{ $settingsUrl }}" class="mt-4">
                @csrf @method('PATCH')
                <div class="flex justify-between text-[11px]"><label for="lux" class="font-semibold">Switch on below</label><span id="lux-out" class="metric font-medium">{{ $room['threshold_lux'] }} lux</span></div>
                <input id="lux" name="lux_threshold" type="range" min="0" max="1000" step="10" value="{{ min(1000, $room['threshold_lux']) }}" @disabled(! $controlsOn) class="mt-2 w-full accent-brass disabled:opacity-40">
                <p class="mt-2 text-[10px] text-stone-500">Above this brightness, daylight is enough and lights stay off.</p>
                @if ($controlsOn) <button class="btn btn-ghost mt-2 !px-2.5 !py-1 !text-[11px]">Save brightness</button> @endif
            </form>

            @unless ($canControl)
                <p class="mt-3 border border-line bg-ink/60 px-2.5 py-2 text-[10px] text-stone-600">You have view-only access. Ask an administrator for Staff access to use these controls.</p>
            @endunless
        </div>
    </section>

    {{-- Schedules + activity --}}
    <section class="mt-3 grid gap-3 lg:grid-cols-[1.7fr_1fr]">
        <div class="border border-line bg-panel p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-semibold">Schedules</h2>
                <a href="{{ route('dashboard.schedules') }}" class="text-[10px] font-semibold text-brass">Manage</a>
            </div>
            @forelse ($schedules as $schedule)
                <div class="mt-3 flex items-center justify-between">
                    <div><p class="text-[11px] font-semibold">{{ $schedule->name }}</p><p class="metric text-[10px] text-stone-500">{{ $schedule->summary }}</p></div>
                    <x-schedule-switch :schedule="$schedule" />
                </div>
            @empty
                <p class="mt-3 text-[11px] text-stone-500">No schedules yet. Lights may turn on at any time. <a href="{{ route('dashboard.schedules') }}" class="font-semibold text-brass">Add a schedule</a>.</p>
            @endforelse
        </div>
        <div class="border border-line bg-panel p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-semibold">Recent activity</h2>
                <a href="{{ route('dashboard.activity') }}" class="text-[10px] font-semibold text-brass">View all</a>
            </div>
            <ul class="mt-3 space-y-2">
                @forelse ($activity as [$text, $time])
                    <li class="flex flex-wrap justify-between gap-x-3 gap-y-1 text-[10px] text-stone-600"><span class="min-w-0 flex-1 break-words">{{ $text }}</span><span class="metric shrink-0 text-stone-500">{{ $time }}</span></li>
                @empty
                    <li class="text-[10px] text-stone-500">Nothing yet. Events appear once the light sends readings.</li>
                @endforelse
            </ul>
        </div>
    </section>

    @if ($device)
        <section class="mt-3 border border-line bg-panel p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-[11px] font-semibold">Where this light is</h2>
                @if ($device->map_url)
                    <a href="{{ $device->map_url }}" target="_blank" rel="noopener" class="text-[10px] font-semibold text-brass">Open in OpenStreetMap</a>
                @endif
            </div>
            @if ($device->hasPosition())
                <x-device-map :device="$device" class="mt-3 h-60" />
                <p class="mt-2 text-[11px] text-stone-600">{{ $device->name }}, {{ $device->location }}@if ($device->area), {{ $device->area }}@endif</p>
            @else
                <p class="mt-3 text-[11px] text-stone-500">
                    No map position yet.
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('dashboard.devices') }}" class="font-semibold text-brass">Set the position on the Devices page</a>.
                    @else
                        Ask an administrator to add it.
                    @endif
                </p>
            @endif
        </section>
    @endif

    <x-slot:scripts>
    <script>
        const lux = document.getElementById('lux');
        lux.addEventListener('input', () => document.getElementById('lux-out').textContent = lux.value + ' lux');
    </script>
    </x-slot:scripts>
</x-layouts.dashboard>
