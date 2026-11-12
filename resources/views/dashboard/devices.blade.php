@php
    $isAdmin = auth()->user()->isAdmin();
    $input = 'w-full rounded-md border border-line bg-ink/60 px-2.5 py-2 text-xs text-white placeholder:text-neutral-600 focus:border-amber focus:outline-none [color-scheme:dark]';
    $lbl = 'mb-1 block text-[10px] font-semibold text-neutral-400';
@endphp
<x-layouts.dashboard :map="true" title="Devices" heading="Devices" sub="Where each light is and whether it is reporting">

    {{-- Shown once, right after a key is made --}}
    @if (session('new_key'))
        <div role="alert" class="mt-4 rounded-lg border border-amber/50 bg-amber/10 p-4">
            <p class="text-xs font-bold text-amber">Key for {{ session('new_key')['device'] }}</p>
            <p class="mt-1 text-[11px] text-neutral-300">Copy this key now. It will not be shown again. If you lose it, make a new one.</p>
            <code class="mt-2 block break-all rounded-md bg-ink px-3 py-2 text-xs text-white">{{ session('new_key')['key'] }}</code>
            <p class="mt-2 text-[10px] text-neutral-400">The device sends readings to <span class="font-semibold text-white">{{ url('/api/readings') }}</span> with the header <span class="font-semibold text-white">Authorization: Bearer (the key)</span>.</p>
        </div>
    @endif

    @if ($errors->any())
        <ul role="alert" class="mt-3 space-y-1 rounded-md border border-red-500/40 bg-red-500/10 px-3 py-2 text-xs text-red-400">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    @endif

    {{-- Add a light (administrators) --}}
    @if ($isAdmin)
        <section class="mt-4 rounded-lg border border-line bg-panel-2 p-4">
            <h2 class="text-[11px] font-semibold">Add a light</h2>
            <form method="POST" action="{{ route('devices.store') }}" class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <div><label for="name" class="{{ $lbl }}">Name</label>
                    <input id="name" name="name" value="{{ old('name') }}" placeholder="Ceiling light" maxlength="60" required class="{{ $input }}"></div>
                <div><label for="location" class="{{ $lbl }}">Room</label>
                    <input id="location" name="location" value="{{ old('location') }}" placeholder="Living room" maxlength="60" required class="{{ $input }}"></div>
                <div><label for="area" class="{{ $lbl }}">Floor or building (optional)</label>
                    <input id="area" name="area" value="{{ old('area') }}" placeholder="2nd floor, Building A" maxlength="100" class="{{ $input }}"></div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <p class="{{ $lbl }}">Where is it on the map? (optional)</p>
                    <x-map-picker :lat="old('latitude')" :lng="old('longitude')" />
                </div>

                <div class="sm:col-span-2 lg:col-span-3"><button class="btn btn-amber w-full sm:w-auto">Add light</button></div>
            </form>
        </section>
    @endif

    {{-- List --}}
    <section class="mt-3 space-y-3">
        @forelse ($devices as $device)
            @php $r = $device->latestReading; @endphp
            <article class="rounded-lg border border-line bg-panel-2 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold">{{ $device->location }}</h2>
                        <p class="text-[11px] text-neutral-400">{{ $device->name }}@if ($device->area), {{ $device->area }}@endif</p>
                        @if ($device->map_url)
                            <a href="{{ $device->map_url }}" target="_blank" rel="noopener" class="mt-1 inline-block text-[11px] font-semibold text-amber">Open in OpenStreetMap</a>
                        @endif
                    </div>
                    @if ($device->isOnline())
                        <span class="flex items-center gap-1.5 rounded-full bg-green-500/15 px-2.5 py-1 text-[11px] font-semibold text-green-400"><span class="size-1.5 rounded-full bg-green-500"></span> Online</span>
                    @else
                        <span class="rounded-full bg-line px-2.5 py-1 text-[11px] font-semibold text-neutral-400">{{ $device->last_seen_at ? 'Offline' : 'No readings yet' }}</span>
                    @endif
                </div>

                @if ($device->hasPosition())
                    <x-device-map :device="$device" class="mt-3 h-52" />
                @elseif ($isAdmin)
                    <p class="mt-3 rounded-md bg-ink/60 px-3 py-2 text-[11px] text-neutral-400">No map position yet. Open "Edit location" below and click the map.</p>
                @endif

                @if ($r)
                    <dl class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach ([
                            ['Brightness', $r->lux . ' lux'],
                            ['Presence', $r->presence ? 'Someone in the room' : 'Empty'],
                            ['Lights', $r->lights_on ? 'On' : 'Off'],
                            ['Power', ($r->power ?? 0) . ' W'],
                        ] as [$k, $v])
                            <div class="rounded-md bg-ink/60 p-2"><dt class="text-[9px] text-neutral-500">{{ $k }}</dt><dd class="text-xs font-semibold">{{ $v }}</dd></div>
                        @endforeach
                    </dl>
                    <p class="mt-2 text-[10px] text-neutral-500">Last reading {{ $r->recorded_at->diffForHumans() }}</p>
                @endif

                @if ($isAdmin)
                    <div class="mt-3 flex flex-wrap items-center gap-3 border-t border-line pt-3">
                        <details class="w-full">
                            <summary class="cursor-pointer text-[11px] font-semibold text-amber">Edit location</summary>
                            <form method="POST" action="{{ route('devices.update', $device) }}" class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @csrf
                                @method('PATCH')
                                <div><label for="n-{{ $device->id }}" class="{{ $lbl }}">Name</label>
                                    <input id="n-{{ $device->id }}" name="name" value="{{ $device->name }}" maxlength="60" required class="{{ $input }}"></div>
                                <div><label for="l-{{ $device->id }}" class="{{ $lbl }}">Room</label>
                                    <input id="l-{{ $device->id }}" name="location" value="{{ $device->location }}" maxlength="60" required class="{{ $input }}"></div>
                                <div><label for="a-{{ $device->id }}" class="{{ $lbl }}">Floor or building</label>
                                    <input id="a-{{ $device->id }}" name="area" value="{{ $device->area }}" maxlength="100" class="{{ $input }}"></div>

                                <div class="sm:col-span-2 lg:col-span-3">
                                    <p class="{{ $lbl }}">Where is it on the map?</p>
                                    <x-map-picker :lat="$device->latitude" :lng="$device->longitude" />
                                </div>

                                <div class="sm:col-span-2 lg:col-span-3"><button class="btn btn-amber w-full sm:w-auto">Save changes</button></div>
                            </form>
                        </details>

                        <form method="POST" action="{{ route('devices.key', $device) }}" onsubmit="return confirm('Make a new key? The old key will stop working.')">
                            @csrf
                            <button class="text-[11px] font-semibold text-neutral-400 hover:text-white">Make a new key</button>
                        </form>
                        <form method="POST" action="{{ route('devices.destroy', $device) }}" onsubmit="return confirm('Delete {{ addslashes($device->name) }} and all of its readings?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-[11px] font-semibold text-neutral-500 hover:text-red-400">Delete</button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <p class="rounded-lg border border-line bg-panel-2 p-4 text-xs text-neutral-400">{{ $isAdmin ? 'No lights yet. Use the form above to add one, and say which room it is in.' : 'No lights have been added yet. Ask an administrator to add one.' }}</p>
        @endforelse
    </section>
</x-layouts.dashboard>
