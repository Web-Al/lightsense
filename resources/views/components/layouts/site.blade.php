@props(['title', 'description' => null, 'cta' => false])
@php
    $links = [
        ['Home', route('home'), request()->routeIs('home')],
        ['How it works', route('home') . '#overview', false],
        ['SDG 7', route('sdg'), request()->routeIs('sdg')],
        ['FAQ', route('faq'), request()->routeIs('faq')],
        ['Testimonials', route('testimonials'), request()->routeIs('testimonials')],
        ['Contact', route('contact'), request()->routeIs('contact')],
    ];
@endphp
<x-layouts.base :title="$title">
<x-slot:head>
    @if ($description) <meta name="description" content="{{ $description }}"> @endif
</x-slot:head>

<a href="#main" class="sr-only z-[60] bg-paper px-3 py-2 text-sm text-ink focus:not-sr-only focus:fixed focus:left-3 focus:top-3">Skip to main content</a>

{{-- Navigation bar: stays at the top while scrolling --}}
<div class="sticky top-0 z-50 border-b border-line bg-ink/90 backdrop-blur">
    <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-3" aria-label="Main">
        <x-logo />
        <div class="hidden items-center gap-5 text-xs text-stone-400 lg:flex">
            @foreach ($links as [$label, $href, $active])
                <a href="{{ $href }}" @if ($active) aria-current="page" @endif class="{{ $active ? 'font-semibold text-white' : 'hover:text-white' }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="hidden items-center gap-4 text-xs lg:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-brass !py-1.5 !text-xs">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-stone-300 hover:text-white">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-brass !py-1.5 !text-xs">Get Started</a>
            @endauth
        </div>
        <button id="menu-btn" class="grid size-10 place-items-center rounded-sm border border-line lg:hidden" aria-label="Open menu" aria-controls="mobile-menu" aria-expanded="false">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </nav>
    <div id="mobile-menu" class="hidden flex-col gap-3 border-t border-line px-5 py-4 text-sm text-stone-300 lg:hidden">
        @foreach ($links as [$label, $href, $active])
            <a href="{{ $href }}">{{ $label }}</a>
        @endforeach
        @auth
            <a href="{{ route('dashboard') }}" class="font-semibold text-brass">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Log in</a>
            <a href="{{ route('register') }}" class="font-semibold text-brass">Get Started</a>
        @endauth
    </div>
</div>

<main id="main" class="min-w-0">{{ $slot }}</main>

<footer class="bg-ink">
    <div class="mx-auto max-w-6xl px-5 {{ $cta ? 'pt-16' : 'pt-10' }}">
        @if ($cta)
            <div class="hero-glow flex flex-col gap-6 border border-line bg-panel p-6 sm:flex-row sm:items-center sm:justify-between sm:p-10">
                <div class="max-w-md">
                    <h2 class="text-2xl font-bold sm:text-3xl">Ready to stop flipping switches?</h2>
                    <p class="mt-2 text-sm text-stone-400">Set up LightSense and let your lights take care of themselves.</p>
                </div>
                <a href="{{ route('register') }}" class="btn btn-brass self-start sm:self-auto">Get Started</a>
            </div>
        @endif

        <div class="flex flex-col gap-4 border-t border-line py-8 text-[11px] text-stone-500 md:flex-row md:items-center md:justify-between {{ $cta ? 'mt-10' : '' }}">
            <x-logo />
            <nav class="flex flex-wrap gap-x-5 gap-y-2" aria-label="Footer">
                @foreach ($links as [$label, $href])
                    <a href="{{ $href }}" class="hover:text-white">{{ $label }}</a>
                @endforeach
            </nav>
            <p class="metric">Supports UN SDG 7 &middot; © {{ date('Y') }} LightSense</p>
        </div>
    </div>
</footer>

<script>
    const menuBtn = document.getElementById('menu-btn'), menu = document.getElementById('mobile-menu');
    function setMenu(open) {
        menu.classList.toggle('hidden', !open); menu.classList.toggle('flex', open);
        menuBtn.setAttribute('aria-expanded', open);
    }
    menuBtn.addEventListener('click', () => setMenu(menu.classList.contains('hidden')));
    menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
</script>
{{ $scripts ?? '' }}
</x-layouts.base>
