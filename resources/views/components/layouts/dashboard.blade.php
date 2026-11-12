@props(['title', 'heading', 'sub' => null, 'live' => false, 'map' => false])
@php
    $initials = collect(explode(' ', auth()->user()->name))->map(fn ($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
    $nav = [
        ['Overview', 'dashboard', 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z'],
        ['Energy', 'dashboard.energy', 'M13 2L4 14h7l-1 8 9-12h-7z'],
        ['Schedules', 'dashboard.schedules', 'M12 6v6l4 2M12 21a9 9 0 100-18 9 9 0 000 18z'],
        ['Activity', 'dashboard.activity', 'M3 12h4l3-8 4 16 3-8h4'],
        ['Devices', 'dashboard.devices', 'M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11zM12 12a2 2 0 100-4 2 2 0 000 4z'],
    ];
    if (auth()->user()->isAdmin()) {
        $nav[] = ['Users', 'dashboard.users', 'M17 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2M10 11a4 4 0 100-8 4 4 0 000 8zM21 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75'];
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
</x-slot:head>
<div class="min-h-screen lg:flex">

    {{-- Sidebar --}}
    <aside class="flex shrink-0 items-center justify-between gap-4 border-b border-line bg-ink p-3 lg:min-h-screen lg:w-48 lg:flex-col lg:items-stretch lg:justify-start lg:border-b-0 lg:border-r">
        <x-logo />
        <nav class="flex gap-1 lg:mt-4 lg:flex-col" aria-label="Dashboard">
            @foreach ($nav as [$label, $route, $icon])
                @php $active = request()->routeIs($route); @endphp
                <a href="{{ route($route) }}" @if($active) aria-current="page" @endif
                   class="flex items-center gap-2 rounded-md px-2.5 py-1.5 text-xs {{ $active ? 'bg-amber/15 text-amber' : 'text-neutral-400 hover:text-white' }}">
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
                    <span class="hidden sm:inline">{{ $label }}</span>
                </a>
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-2 lg:mt-auto">
            @csrf
            <span class="grid size-6 place-items-center rounded-full bg-amber text-[10px] font-bold text-black">{{ $initials }}</span>
            <span class="hidden min-w-0 sm:block lg:flex-1">
                <span class="block truncate text-[11px] text-neutral-300">{{ auth()->user()->name }}</span>
                <span class="block text-[10px] text-neutral-500">{{ auth()->user()->role_label }}</span>
            </span>
            <button class="text-neutral-400 hover:text-white" aria-label="Log out">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            </button>
        </form>
    </aside>

    {{-- Main --}}
    <main class="flex-1 bg-panel p-4 sm:p-5">
        <header class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-base font-bold">{{ $heading }}</h1>
                @if ($sub)
                    <p class="mt-0.5 flex items-center gap-1.5 text-[11px] text-neutral-400">
                        @if ($live) <span class="size-1.5 rounded-full bg-green-500"></span> @endif
                        {{ $sub }}
                    </p>
                @endif
            </div>
            @isset($actions) {{ $actions }} @endisset
        </header>

        @if (session('status'))
            <div role="status" class="mt-3 rounded-md border border-amber/40 bg-amber/10 px-3 py-2 text-xs text-amber">{{ session('status') }}</div>
        @endif

        {{ $slot }}
    </main>
</div>
{{ $scripts ?? '' }}
</x-layouts.base>
