@php
    $nav = [
        ['grid', 'Overview', '#overview', true],
        ['bars', 'Energy', '#energy', false],
        ['clock', 'Schedules', '#schedules', false],
        ['activity', 'Activity', '#activity', false],
    ];

    $user = auth()->user();
@endphp

<x-layouts.app title="Dashboard | LightSense">
    <div class="min-h-screen lg:flex">

        {{-- Sidebar (desktop) --}}
        <aside class="hidden w-56 shrink-0 flex-col bg-ink px-4 py-5 lg:flex">
            <a href="{{ route('home') }}" aria-label="LightSense home" class="px-1"><x-logo /></a>

            <nav class="mt-8 flex flex-col gap-1" aria-label="Dashboard">
                @foreach ($nav as [$icon, $label, $href, $active])
                    <a href="{{ $href }}"
                       @if ($active) aria-current="page" @endif
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ $active ? 'bg-brand/10 font-medium text-brand' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                        <x-icon :name="$icon" class="size-4" />
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto flex items-center gap-3 px-1 pt-6">
                <span class="grid size-8 shrink-0 place-items-center rounded-full bg-brand text-[11px] font-bold text-ink">{{ $user->initials }}</span>
                <span class="min-w-0 flex-1 truncate text-xs font-medium text-white/80">{{ $user->short_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" aria-label="Log out" class="grid size-8 place-items-center rounded-lg text-white/50 transition hover:bg-white/5 hover:text-white">
                        <x-icon name="logout" class="size-4" />
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0 flex-1">

            {{-- Top bar (mobile) --}}
            <header class="sticky top-0 z-30 border-b border-white/10 bg-ink/95 backdrop-blur lg:hidden">
                <div class="flex h-14 items-center justify-between px-4">
                    <a href="{{ route('home') }}" aria-label="LightSense home"><x-logo /></a>
                    <div class="flex items-center gap-2">
                        <span class="grid size-8 place-items-center rounded-full bg-brand text-[11px] font-bold text-ink">{{ $user->initials }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" aria-label="Log out" class="grid size-8 place-items-center rounded-lg text-white/60 hover:text-white">
                                <x-icon name="logout" class="size-4" />
                            </button>
                        </form>
                    </div>
                </div>
                <nav class="flex gap-2 overflow-x-auto px-4 pb-3" aria-label="Dashboard">
                    @foreach ($nav as [$icon, $label, $href, $active])
                        <a href="{{ $href }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 text-xs {{ $active ? 'bg-brand/15 font-medium text-brand' : 'bg-white/5 text-white/70' }}">
                            <x-icon :name="$icon" class="size-3.5" />
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </header>

            <main class="min-h-screen bg-panel p-4 sm:p-6 lg:m-3 lg:ml-0 lg:min-h-[calc(100vh-1.5rem)] lg:rounded-3xl lg:p-8">

                @if (! $device)
                    {{-- No rooms yet --}}
                    <section class="mx-auto mt-6 max-w-md rounded-2xl bg-card p-6 text-center lg:mt-16">
                        <span class="mx-auto grid size-10 place-items-center rounded-xl bg-brand/15 text-brand"><x-icon name="bulb" class="size-5" /></span>
                        <h1 class="mt-4 text-lg font-bold">Add your first room</h1>
                        <p class="mt-1.5 text-sm text-white/55">Each room has its own LightSense controller. Name the room to start tracking it.</p>

                        <form method="POST" action="{{ route('devices.store') }}" class="mt-5 flex flex-col gap-3 text-left sm:flex-row">
                            @csrf
                            <label for="room-name" class="sr-only">Room name</label>
                            <input id="room-name" name="name" type="text" required maxlength="60" value="{{ old('name') }}" placeholder="e.g. Living room"
                                   class="block w-full rounded-lg border border-white/10 bg-panel px-3.5 py-2.5 text-sm text-white placeholder:text-white/35 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/30">
                            <button type="submit" class="btn btn-primary shrink-0">Add room</button>
                        </form>
                        @error('name') <p class="mt-2 text-left text-xs text-red-400">{{ $message }}</p> @enderror
                    </section>
                @else
                    <div data-dashboard
                         data-live-url="{{ route('devices.live', $device) }}"
                         data-update-url="{{ route('devices.update', $device) }}"
                         data-snapshot="{{ json_encode($snapshot) }}">

                        {{-- Header --}}
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                            <div class="min-w-0">
                                <h1 class="truncate text-xl font-bold tracking-tight">{{ $device->name }}</h1>
                                <p class="mt-1 flex items-center gap-2 text-xs text-white/55">
                                    <span data-live-dot class="size-1.5 shrink-0 rounded-full {{ $snapshot['online'] ? 'bg-online' : 'bg-white/30' }}"></span>
                                    <span data-live="statusText">{{ $snapshot['statusText'] }}</span>
                                </p>
                            </div>

                            <form method="GET" action="{{ route('dashboard') }}" class="relative w-full min-w-0 sm:w-auto sm:shrink-0">
                                <label for="device" class="sr-only">Room</label>
                                <select id="device" name="device" onchange="this.form.submit()"
                                        class="appearance-none w-full max-w-full rounded-lg border border-white/10 bg-card py-2 pl-3 pr-8 text-xs font-medium text-white [color-scheme:dark] focus:outline-none focus:ring-2 focus:ring-brand/40 sm:w-auto">
                                    @foreach ($devices as $room)
                                        <option value="{{ $room->id }}" @selected($room->id === $device->id)>{{ $room->name }}</option>
                                    @endforeach
                                </select>
                                <x-icon name="chevron-down" class="pointer-events-none absolute right-2.5 top-1/2 size-3.5 -translate-y-1/2 text-white/50" />
                            </form>
                        </div>

                        {{-- Live numbers --}}
                        <section id="overview" class="mt-6 grid scroll-mt-20 grid-cols-2 gap-3 xl:grid-cols-4">
                            <article class="dash-card">
                                <p class="dash-label">Lights</p>
                                <p class="mt-2 break-words text-xl font-bold tracking-tight sm:text-2xl" data-live="lightsLabel">{{ $snapshot['lightsLabel'] }}</p>
                                <button type="button" data-action="toggle-lights" data-live="lightsButton"
                                        class="btn btn-primary btn-xs mt-3">{{ $snapshot['lightsButton'] }}</button>
                            </article>

                            <article class="dash-card">
                                <p class="dash-label">Presence</p>
                                <p class="mt-2 break-words text-xl font-bold tracking-tight sm:text-2xl" data-live="presenceLabel">{{ $snapshot['presenceLabel'] }}</p>
                                <p class="mt-3 text-[11px] text-white/45" data-live="lastMovement">{{ $snapshot['lastMovement'] }}</p>
                            </article>

                            <article class="dash-card">
                                <p class="dash-label">Room brightness</p>
                                <p class="mt-2 break-words text-xl font-bold tracking-tight sm:text-2xl">
                                    <span data-live="lux">{{ $snapshot['lux'] }}</span>
                                    <span class="text-sm font-medium text-white/45">lux</span>
                                </p>
                                <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-white/10">
                                    <div class="h-full rounded-full bg-brand transition-[width] duration-500" data-live-width="luxPct" style="width: {{ $snapshot['luxPct'] }}%"></div>
                                </div>
                            </article>

                            <article class="dash-card">
                                <p class="dash-label">Power now</p>
                                <p class="mt-2 break-words text-xl font-bold tracking-tight sm:text-2xl">
                                    <span data-live="power">{{ $snapshot['power'] }}</span>
                                    <span class="text-sm font-medium text-white/45">W</span>
                                </p>
                                <p class="mt-3 text-[11px] text-white/45">Today: <span data-live="todayKwh">{{ $snapshot['todayKwh'] }}</span> kWh</p>
                            </article>
                        </section>

                        <div class="mt-3 grid gap-3 lg:grid-cols-3">

                            {{-- Power chart --}}
                            <section id="energy" class="dash-card scroll-mt-20 lg:col-span-2">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-sm font-semibold">Power use</h2>
                                    <span class="text-[11px] text-white/45">Last 60 seconds</span>
                                </div>

                                <div class="mt-5 flex gap-3">
                                    <ul data-chart-labels class="flex h-40 w-10 shrink-0 flex-col justify-between text-right text-[10px] text-white/40" aria-hidden="true"></ul>
                                    <div class="min-w-0 flex-1">
                                        <svg data-chart viewBox="0 0 600 160" preserveAspectRatio="none" class="h-40 w-full overflow-visible" role="img" aria-label="Power use in watts over the last 60 seconds"></svg>
                                    </div>
                                </div>

                                <dl class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div class="rounded-xl bg-black/20 px-3 py-3 sm:px-4">
                                        <dt class="text-[11px] text-white/45">Voltage</dt>
                                        <dd class="mt-1 text-sm font-semibold"><span data-live="voltage">{{ $snapshot['voltage'] }}</span> V</dd>
                                    </div>
                                    <div class="rounded-xl bg-black/20 px-3 py-3 sm:px-4">
                                        <dt class="text-[11px] text-white/45">Current</dt>
                                        <dd class="mt-1 text-sm font-semibold"><span data-live="current">{{ $snapshot['current'] }}</span> A</dd>
                                    </div>
                                    <div class="rounded-xl bg-black/20 px-3 py-3 sm:px-4">
                                        <dt class="text-[11px] text-white/45">Energy this month</dt>
                                        <dd class="mt-1 text-sm font-semibold"><span data-live="monthKwh">{{ $snapshot['monthKwh'] }}</span> kWh</dd>
                                    </div>
                                </dl>
                            </section>

                            {{-- Light control --}}
                            <section class="dash-card">
                                <h2 class="text-sm font-semibold">Light control</h2>

                                <div class="mt-4 grid grid-cols-2 rounded-lg bg-black/25 p-1 text-xs" role="group" aria-label="Control mode">
                                    @foreach (['auto' => 'Automatic', 'manual' => 'Manual'] as $value => $label)
                                        <button type="button" data-action="set-mode" data-mode="{{ $value }}" aria-pressed="{{ $snapshot['mode'] === $value ? 'true' : 'false' }}"
                                                class="rounded-md px-3 py-1.5 transition {{ $snapshot['mode'] === $value ? 'bg-brand font-semibold text-ink' : 'text-white/60 hover:text-white' }}">{{ $label }}</button>
                                    @endforeach
                                </div>

                                <p class="mt-3 text-xs leading-relaxed text-white/50" data-live="modeHelp">{{ $snapshot['modeHelp'] }}</p>

                                <div class="mt-5 flex items-center justify-between text-xs">
                                    <label for="lux-threshold" class="font-medium text-white/80">Switch on below</label>
                                    <span class="font-semibold"><span data-live="luxThreshold">{{ $snapshot['luxThreshold'] }}</span> lux</span>
                                </div>
                                <input id="lux-threshold" data-lux-slider type="range" min="50" max="1000" step="10" value="{{ $snapshot['luxThreshold'] }}"
                                       class="mt-3 w-full accent-brand">
                                <p class="mt-2 text-[11px] leading-relaxed text-white/45">Above this brightness, daylight is enough and lights stay off.</p>
                            </section>

                            {{-- Schedules --}}
                            <section id="schedules" class="dash-card scroll-mt-20 lg:col-span-2">
                                <h2 class="text-sm font-semibold">Schedules</h2>

                                <ul class="mt-3 divide-y divide-white/10">
                                    @forelse ($schedules as $schedule)
                                        <li class="flex items-center justify-between gap-3 py-3 sm:gap-4">
                                            <div class="min-w-0">
                                                <p class="break-words text-sm font-medium">{{ $schedule->name }}</p>
                                                <p class="mt-0.5 break-words text-xs text-white/50">{{ $schedule->description }}</p>
                                            </div>
                                            <button type="button" role="switch"
                                                    aria-checked="{{ $schedule->enabled ? 'true' : 'false' }}"
                                                    aria-label="{{ $schedule->name }}"
                                                    data-action="toggle-schedule" data-schedule-id="{{ $schedule->id }}"
                                                    data-url="{{ route('schedules.toggle', $schedule) }}"
                                                    class="group relative grid size-11 shrink-0 place-items-center rounded-lg focus-visible:outline">
                                                <span class="relative h-5 w-9 rounded-full bg-white/20 transition group-aria-checked:bg-brand">
                                                    <span class="absolute left-0.5 top-0.5 size-4 rounded-full bg-white shadow transition-transform group-aria-checked:translate-x-4"></span>
                                                </span>
                                            </button>
                                        </li>
                                    @empty
                                        <li class="py-3 text-sm text-white/50">No schedules yet.</li>
                                    @endforelse
                                </ul>
                            </section>

                            {{-- Recent activity --}}
                            <section id="activity" class="dash-card scroll-mt-20">
                                <h2 class="text-sm font-semibold">Recent activity</h2>
                                <ul class="mt-3 divide-y divide-white/10" data-activity>
                                    @forelse ($snapshot['activity'] as $entry)
                                        <li class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 py-2.5 text-sm">
                                            <span class="min-w-0 flex-1 break-words">{{ $entry['message'] }}</span>
                                            <span class="shrink-0 text-xs text-white/40">{{ $entry['time'] }}</span>
                                        </li>
                                    @empty
                                        <li class="py-2.5 text-sm text-white/50">Nothing yet.</li>
                                    @endforelse
                                </ul>
                            </section>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
</x-layouts.app>
