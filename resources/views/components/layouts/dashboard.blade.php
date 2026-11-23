@props(['title', 'heading', 'sub' => null, 'live' => false, 'map' => false, 'calendar' => false])
@php
    $initials = collect(explode(' ', auth()->user()->name))->map(fn ($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
    $nav = [
        ['Overview', 'dashboard', 'M7 7h10v10H7zM10 3v4M14 3v4M10 17v4M14 17v4M3 10h4M3 14h4M17 10h4M17 14h4'],
        ['Energy', 'dashboard.energy', 'M2 12h8M14 12h8M10 6v12M14 6v12'],
        ['Schedules', 'dashboard.schedules', 'M2 12h6M16 12h6M8 7v10M16 7v10M10.5 8h3v8h-3z'],
        ['Activity', 'dashboard.activity', 'M3 5h18v14H3zM5 13h3l2-5 3 8 2-4h4'],
        ['Devices', 'dashboard.devices', 'M12 4a8 8 0 100 16 8 8 0 000-16zM6.3 6.3l11.4 11.4M17.7 6.3L6.3 17.7M2 12h2M20 12h2'],
    ];
    if (auth()->user()->isAdmin()) {
        $nav[] = ['Users', 'dashboard.users', 'M12 11a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM5 20v-2a4 4 0 014-4h6a4 4 0 014 4v2'];
    }
@endphp
<x-layouts.base :title="$title">
<x-slot:head>
    @if ($map)
        {{-- Leaflet: open-source map library --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script src="{{ asset('js/maps.js') }}" defer></script>
    @endif
    @if ($calendar)
        {{-- FullCalendar: open-source calendar (MIT licence) --}}
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    @endif
</x-slot:head>
<div class="min-h-screen lg:flex">

    {{-- Sidebar --}}
    <aside class="flex shrink-0 flex-wrap items-center justify-between gap-x-3 gap-y-2 border-b border-line bg-ink p-3 lg:min-h-screen lg:w-48 lg:flex-nowrap lg:flex-col lg:items-stretch lg:justify-start lg:gap-4 lg:border-b-0 lg:border-r">
        <x-logo />
        <nav class="order-last flex w-full min-w-0 gap-1 overflow-x-auto pb-1 lg:order-none lg:mt-4 lg:w-auto lg:flex-col lg:overflow-visible lg:pb-0" aria-label="Dashboard">
            @foreach ($nav as [$label, $route, $icon])
                @php $active = request()->routeIs($route); @endphp
                <a href="{{ route($route) }}" @if($active) aria-current="page" @endif
                   class="flex min-h-10 shrink-0 items-center gap-2 whitespace-nowrap rounded-sm px-2.5 py-1.5 text-xs {{ $active ? 'bg-brass/15 text-brass' : 'text-stone-600 hover:text-stone-900' }}">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter"><path d="{{ $icon }}"/></svg>
                    <span class="hidden sm:inline">{{ $label }}</span>
                </a>
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="ml-auto flex shrink-0 items-center gap-2 lg:ml-0 lg:mt-auto">
            @csrf
            <span class="grid size-6 place-items-center rounded-sm bg-brass text-[10px] font-bold text-white">{{ $initials }}</span>
            <span class="hidden min-w-0 sm:block lg:flex-1">
                <span class="block truncate text-[11px] text-stone-700">{{ auth()->user()->name }}</span>
                <span class="block text-[10px] text-stone-500">{{ auth()->user()->role_label }}</span>
            </span>
            <button class="grid size-10 shrink-0 place-items-center text-stone-600 hover:text-stone-900 lg:size-8" aria-label="Log out">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            </button>
        </form>
    </aside>

    {{-- Main --}}
    <main class="min-w-0 flex-1 bg-paper p-3 sm:p-5 xl:p-6">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="break-words text-base font-bold">{{ $heading }}</h1>
                @if ($sub)
                    <p class="mt-0.5 flex items-start gap-1.5 break-words text-[11px] text-stone-600">
                        @if ($live) <span class="size-1.5 shrink-0 rounded-full bg-live pulse-live"></span> @endif
                        {{ $sub }}
                    </p>
                @endif
            </div>
            @isset($actions) <div class="w-full min-w-0 sm:w-auto sm:max-w-[50%]">{{ $actions }}</div> @endisset
        </header>

        {{ $slot }}
    </main>
</div>
{{ $scripts ?? '' }}
</x-layouts.base>
