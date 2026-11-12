@php $canControl = auth()->user()->isStaff(); @endphp
<x-layouts.dashboard :map="true" title="Overview" :heading="$room['name']" :sub="$sub" :live="$device?->isOnline() ?? false">
    <x-slot:actions>
        @if ($devices->count() > 1)
            <form method="GET" action="{{ route('dashboard') }}">
                <select name="device" onchange="this.form.submit()" class="rounded-md border border-line bg-panel-2 px-2 py-1 text-[11px] text-neutral-300" aria-label="Choose a light">
                    @foreach ($devices as $d)
                        <option value="{{ $d->id }}" @selected($device && $d->id === $device->id)>{{ $d->location }} - {{ $d->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </x-slot:actions>

    {{-- Stat cards --}}
    <section class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-lg border border-line bg-panel-2 p-3">
            <p class="text-[10px] text-neutral-500">Lights</p>
            <p id="light-state" class="mt-1 text-xl font-bold">{{ $room['lights_on'] ? 'On' : 'Off' }}</p>
            <button id="light-btn" @disabled(! $canControl) class="btn btn-amber mt-2 !px-2.5 !py-1 !text-[11px] disabled:cursor-not-allowed disabled:opacity-40">{{ $room['lights_on'] ? 'Turn off' : 'Turn on' }}</button>
        </div>
        <div class="rounded-lg border border-line bg-panel-2 p-3">
            <p class="text-[10px] text-neutral-500">Presence</p>
            <p class="mt-1 text-xl font-bold">{{ $room['presence'] }}</p>
            <p class="mt-2 text-[10px] text-neutral-500">Last movement: none yet</p>
        </div>
        <div class="rounded-lg border border-line bg-panel-2 p-3">
            <p class="text-[10px] text-neutral-500">Room brightness</p>
            <p class="mt-1 text-xl font-bold">{{ $room['brightness_lux'] }} <span class="text-[10px] font-normal text-neutral-500">lux</span></p>
            <div class="mt-3 h-1 rounded-full bg-line"><div class="h-1 rounded-full bg-amber" style="width: {{ $room['brightness_pct'] }}%"></div></div>
        </div>
        <div class="rounded-lg border border-line bg-panel-2 p-3">
            <p class="text-[10px] text-neutral-500">Power now</p>
            <p class="mt-1 text-xl font-bold">{{ $room['power_w'] }} <span class="text-[10px] font-normal text-neutral-500">W</span></p>
            <p class="mt-2 text-[10px] text-neutral-500">Today: <span class="font-semibold text-white">{{ $room['today_kwh'] }} kWh</span></p>
        </div>
    </section>

    {{-- Power use + light control --}}
    <section class="mt-3 grid gap-3 lg:grid-cols-[1.7fr_1fr]">
        <div class="rounded-lg border border-line bg-panel-2 p-4">
            <div class="flex justify-between text-[11px]"><h2 class="font-semibold">Power use</h2><span class="text-neutral-500">Last 48 seconds</span></div>
            <svg viewBox="0 0 400 120" class="mt-3 h-36 w-full" preserveAspectRatio="none" role="img" aria-label="Power use chart">
                @foreach ([10, 34, 58, 82, 106] as $y)
                    <line x1="28" x2="400" y1="{{ $y }}" y2="{{ $y }}" stroke="#2c2d33" stroke-width="1"/>
                @endforeach
                <polyline fill="none" stroke="#f5a524" stroke-width="2" points="28,106 400,106"/>
            </svg>
            <div class="mt-3 grid grid-cols-3 gap-2">
                @foreach ([['Voltage', $room['voltage'].' V'], ['Current', number_format($room['current'], 2).' A'], ['Energy this month', $room['month_kwh'].' kWh']] as [$k, $v])
                    <div class="rounded-md bg-ink/60 p-2"><p class="text-[9px] text-neutral-500">{{ $k }}</p><p class="text-xs font-semibold">{{ $v }}</p></div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-line bg-panel-2 p-4">
            <h2 class="text-[11px] font-semibold">Light control</h2>
            <div class="mt-3 grid grid-cols-2 rounded-md bg-ink/60 p-0.5 text-[11px]">
                <button data-mode="automatic" @disabled(! $canControl) class="rounded bg-amber py-1 font-semibold text-black disabled:cursor-not-allowed disabled:opacity-60">Automatic</button>
                <button data-mode="manual" @disabled(! $canControl) class="rounded py-1 text-neutral-400 disabled:cursor-not-allowed">Manual</button>
            </div>
            <p class="mt-3 text-[10px] leading-relaxed text-neutral-500">Lights switch on when someone is in the room and it is dark.</p>
            <div class="mt-4 flex justify-between text-[11px]"><span class="font-semibold">Switch on below</span><span id="lux-out" class="font-semibold">{{ $room['threshold_lux'] }} lux</span></div>
            <input id="lux" type="range" min="0" max="1000" value="{{ $room['threshold_lux'] }}" @disabled(! $canControl) class="mt-2 w-full accent-amber disabled:opacity-40" aria-label="Brightness threshold">
            <p class="mt-2 text-[10px] text-neutral-500">Above this brightness, daylight is enough and lights stay off.</p>
            @unless ($canControl)
                <p class="mt-3 rounded-md bg-ink/60 px-2.5 py-2 text-[10px] text-neutral-400">You have view-only access. Ask an administrator for Staff access to use these controls.</p>
            @endunless
        </div>
    </section>

    {{-- Schedules + activity --}}
    <section class="mt-3 grid gap-3 lg:grid-cols-[1.7fr_1fr]">
        <div class="rounded-lg border border-line bg-panel-2 p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-semibold">Schedules</h2>
                <a href="{{ route('dashboard.schedules') }}" class="text-[10px] font-semibold text-amber">Manage</a>
            </div>
            @forelse ($schedules as $schedule)
                <div class="mt-3 flex items-center justify-between">
                    <div><p class="text-[11px] font-semibold">{{ $schedule->name }}</p><p class="text-[10px] text-neutral-500">{{ $schedule->summary }}</p></div>
                    <x-schedule-switch :schedule="$schedule" />
                </div>
            @empty
                <p class="mt-3 text-[11px] text-neutral-500">No schedules yet. <a href="{{ route('dashboard.schedules') }}" class="font-semibold text-amber">Add your first schedule</a>.</p>
            @endforelse
        </div>
        <div class="rounded-lg border border-line bg-panel-2 p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-[11px] font-semibold">Recent activity</h2>
                <a href="{{ route('dashboard.activity') }}" class="text-[10px] font-semibold text-amber">View all</a>
            </div>
            <ul class="mt-3 space-y-2">
                @foreach ($activity as [$text, $time])
                    <li class="flex justify-between text-[10px] text-neutral-400"><span>{{ $text }}</span><span>{{ $time }}</span></li>
                @endforeach
            </ul>
        </div>
    </section>

    @if ($device)
        <section class="mt-3 rounded-lg border border-line bg-panel-2 p-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-[11px] font-semibold">Where this light is</h2>
                @if ($device->map_url)
                    <a href="{{ $device->map_url }}" target="_blank" rel="noopener" class="text-[10px] font-semibold text-amber">Open in OpenStreetMap</a>
                @endif
            </div>
            @if ($device->hasPosition())
                <x-device-map :device="$device" class="mt-3 h-60" />
                <p class="mt-2 text-[11px] text-neutral-400">{{ $device->name }}, {{ $device->location }}@if ($device->area), {{ $device->area }}@endif</p>
            @else
                <p class="mt-3 text-[11px] text-neutral-500">
                    No map position yet.
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('dashboard.devices') }}" class="font-semibold text-amber">Set the position on the Devices page</a>.
                    @else
                        Ask an administrator to add it.
                    @endif
                </p>
            @endif
        </section>
    @endif

    <x-slot:scripts>
    <script>
        // These controls only change the page for now. Saving them comes later.
        const btn = document.getElementById('light-btn'), state = document.getElementById('light-state');
        btn.addEventListener('click', () => {
            const on = state.textContent.trim() === 'On';
            state.textContent = on ? 'Off' : 'On';
            btn.textContent = on ? 'Turn on' : 'Turn off';
        });
        document.querySelectorAll('[data-mode]').forEach(b => b.addEventListener('click', () => {
            document.querySelectorAll('[data-mode]').forEach(o => o.className = 'rounded py-1 text-neutral-400');
            b.className = 'rounded bg-amber py-1 font-semibold text-black';
        }));
        const lux = document.getElementById('lux');
        lux.addEventListener('input', () => document.getElementById('lux-out').textContent = lux.value + ' lux');
    </script>
    </x-slot:scripts>
</x-layouts.dashboard>
