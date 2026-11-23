@php
    $isAdmin = auth()->user()->isAdmin();
    $input = 'w-full rounded-sm border border-line bg-ink/60 px-2.5 py-2 text-xs text-stone-900 placeholder:text-stone-600 focus:border-brass focus:outline-none [color-scheme:light]';
    $lbl = 'mb-1 block text-[10px] font-semibold text-stone-600';
@endphp
<x-layouts.dashboard :map="true" title="Devices" heading="Devices" sub="Where each light is and whether it is reporting">

    {{-- Add a light (administrators) --}}
    @if ($isAdmin)
        <section class="mt-4 rounded-sm border border-line bg-panel-2 p-4">
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

                <div class="sm:col-span-2 lg:col-span-3"><button class="btn btn-brass w-full sm:w-auto">Add light</button></div>
            </form>
        </section>
    @endif

    {{-- List --}}
    <section class="mt-3 space-y-3">
        @forelse ($devices as $device)
            @php $r = $device->latestReading; @endphp
            <article class="rounded-sm border border-line bg-panel-2 p-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold">{{ $device->location }}</h2>
                        <p class="text-[11px] text-stone-600">{{ $device->name }}@if ($device->area), {{ $device->area }}@endif</p>
                        @if ($device->map_url)
                            <a href="{{ $device->map_url }}" target="_blank" rel="noopener" class="mt-1 inline-block text-[11px] font-semibold text-brass">Open in OpenStreetMap</a>
                        @endif
                    </div>
                    @if ($device->isOnline())
                        <span class="metric flex items-center gap-1.5 rounded-sm border border-live/30 bg-live/10 px-2.5 py-1 text-[11px] font-semibold text-live"><span class="size-1.5 rounded-full bg-live pulse-live"></span> Online</span>
                    @else
                        <span class="rounded-sm bg-line px-2.5 py-1 text-[11px] font-semibold text-stone-600">{{ $device->last_seen_at ? 'Offline' : 'No readings yet' }}</span>
                    @endif
                </div>

                @if ($device->hasPosition())
                    <x-device-map :device="$device" class="mt-3 h-52" />
                @elseif ($isAdmin)
                    <p class="mt-3 rounded-sm bg-ink/60 px-3 py-2 text-[11px] text-stone-600">No map position yet. Open "Edit location" below and click the map.</p>
                @endif

                @if ($r)
                    <dl class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach ([
                            ['Brightness', $r->lux . ' lux'],
                            ['Presence', $r->presence ? 'Someone in the room' : 'Empty'],
                            ['Lights', $r->lights_on ? 'On' : 'Off'],
                            ['Power', ($r->power ?? 0) . ' W'],
                        ] as [$k, $v])
                            <div class="rounded-sm border border-line bg-ink/60 p-2"><dt class="metric text-[9px] text-stone-500">{{ $k }}</dt><dd class="metric text-xs font-semibold {{ $k === 'Lights' && $r->lights_on ? 'text-live' : '' }}">{{ $v }}</dd></div>
                        @endforeach
                    </dl>
                    <p class="mt-2 text-[10px] text-stone-500">Last reading {{ $r->recorded_at->diffForHumans() }}</p>
                @endif

                @if ($isAdmin)
                    <div class="mt-3 flex flex-wrap items-center gap-3 border-t border-line pt-3">
                        <details class="w-full">
                            <summary class="cursor-pointer text-[11px] font-semibold text-brass">Edit location</summary>
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

                                <div class="sm:col-span-2 lg:col-span-3"><button class="btn btn-brass w-full sm:w-auto">Save changes</button></div>
                            </form>
                        </details>

                        <form method="POST" action="{{ route('devices.key', $device) }}" onsubmit="return confirm('Make a new key? The old key will stop working.')">
                            @csrf
                            <button class="min-h-10 px-2 text-left text-[11px] font-semibold text-stone-600 hover:text-stone-900">Make a new key</button>
                        </form>
                        <form method="POST" action="{{ route('devices.destroy', $device) }}" onsubmit="return confirm('Delete {{ addslashes($device->name) }} and all of its readings?')">
                            @csrf
                            @method('DELETE')
                            <button class="min-h-10 px-2 text-left text-[11px] font-semibold text-stone-500 hover:text-ember">Delete</button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <p class="rounded-sm border border-line bg-panel-2 p-4 text-xs text-stone-600">{{ $isAdmin ? 'No lights yet. Use the form above to add one, and say which room it is in.' : 'No lights have been added yet. Ask an administrator to add one.' }}</p>
        @endforelse
    </section>
</x-layouts.dashboard>
