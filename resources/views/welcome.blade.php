<x-layouts.base title="IoT Based Automatic Light System">

{{-- Navigation bar: stays at the top while scrolling --}}
<div class="sticky top-0 z-50 border-b border-line bg-ink/90 backdrop-blur">
    <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
        <x-logo />
        <div class="hidden items-center gap-6 text-xs text-neutral-400 md:flex">
            <a href="#top" class="font-semibold text-white">Home</a>
            <a href="#overview" class="hover:text-white">Overview</a>
            <a href="#features" class="hover:text-white">Features</a>
            <a href="#hardware" class="hover:text-white">Hardware</a>
        </div>
        <div class="hidden items-center gap-4 text-xs md:flex">
            <a href="{{ route('login') }}" class="text-neutral-300 hover:text-white">Log in</a>
            <a href="{{ route('register') }}" class="btn btn-amber !py-1.5 !text-xs">Get Started</a>
        </div>
        <button id="menu-btn" class="grid size-8 place-items-center rounded-md border border-line md:hidden" aria-label="Menu">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </nav>
    <div id="mobile-menu" class="hidden flex-col gap-3 border-t border-line px-5 py-4 text-sm text-neutral-300 md:hidden">
        <a href="#overview">Overview</a><a href="#features">Features</a><a href="#hardware">Hardware</a>
        <a href="{{ route('login') }}">Log in</a>
    </div>

</div>

{{-- Hero --}}
<header class="bg-ink">
    <div id="top" class="mx-auto max-w-6xl px-5 pb-20 pt-14 sm:pt-20">
        <p class="mb-4 inline-flex rounded-full border border-amber/40 bg-amber/10 px-2.5 py-0.5 text-[10px] font-semibold text-amber md:hidden">IoT · Arduino · Energy-aware lighting</p>
        <h1 class="max-w-xl text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl">IoT Based Automatic <span class="text-amber">Light System</span></h1>
        <p class="mt-5 max-w-md text-sm leading-relaxed text-neutral-400">LightSense is an automatic light system that reads room brightness and presence, then switches your lights for you.</p>
        <div class="mt-7 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('register') }}" class="btn btn-amber">Get Started</a>
            <a href="#overview" class="btn btn-ghost">See how it works</a>
        </div>
    </div>
</header>

{{-- Overview --}}
<section id="overview" class="bg-white py-16 text-neutral-900">
    <div class="mx-auto max-w-6xl px-5">
        <p class="eyebrow !text-amber-dark">Overview</p>
        <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">From movement to light in three steps</h2>
        <p class="mt-2 text-sm text-neutral-500">LightSense reads the room, decides, and acts – no wall switch needed.</p>

        <div class="mt-8 grid gap-4 md:grid-cols-3">
            @foreach ($steps as $i => [$title, $text])
                <article class="rounded-xl border border-paper-line bg-paper p-5">
                    <span class="grid size-6 place-items-center rounded-full bg-amber text-xs font-bold text-black">{{ $i + 1 }}</span>
                    <h3 class="mt-4 text-sm font-bold">{{ $title }}</h3>
                    <p class="mt-2 text-xs leading-relaxed text-neutral-500">{{ $text }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Features --}}
<section id="features" class="bg-panel py-16">
    <div class="mx-auto max-w-6xl px-5">
        <p class="eyebrow">Features</p>
        <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Everything your lighting needs to run itself</h2>
        <p class="mt-2 text-sm text-neutral-400">Sensing, scheduling and monitoring in a single low-cost system.</p>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-10 lg:gap-y-8">
            @foreach ($features as [$title, $text, $icon])
                <article class="rounded-xl border border-line bg-panel-2 p-5 lg:border-0 lg:bg-transparent lg:p-0">
                    <span class="grid size-7 place-items-center rounded-md bg-amber/15 text-amber">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
                    </span>
                    <h3 class="mt-3 text-sm font-bold">{{ $title }}</h3>
                    <p class="mt-1.5 text-xs leading-relaxed text-neutral-400">{{ $text }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Hardware --}}
<section id="hardware" class="bg-white py-16 text-neutral-900">
    <div class="mx-auto max-w-6xl px-5">
        <p class="eyebrow !text-amber-dark">Hardware</p>
        <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Built from affordable, proven components</h2>
        <p class="mt-2 text-sm text-neutral-500">An Arduino-based build that is easy to source, wire and maintain.</p>

        <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($hardware as [$tag, $name, $text])
                <article class="rounded-xl border border-paper-line bg-paper p-4">
                    <span class="chip">{{ $tag }}</span>
                    <h3 class="mt-2 text-sm font-bold">{{ $name }}</h3>
                    <p class="mt-1 text-[11px] leading-relaxed text-neutral-500">{{ $text }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA + footer --}}
<footer class="bg-ink">
    <div class="mx-auto max-w-6xl px-5 pt-14">
        <div class="flex flex-col gap-5 rounded-2xl bg-amber p-6 text-black sm:flex-row sm:items-center sm:justify-between sm:p-8">
            <div class="max-w-md">
                <h2 class="text-xl font-extrabold sm:text-2xl">Ready to stop flipping switches?</h2>
                <p class="mt-1.5 text-xs text-black/70">Set up LightSense and let your lights take care of themselves.</p>
            </div>
            <a href="{{ route('register') }}" class="btn btn-dark self-start sm:self-auto">Get Started</a>
        </div>

        <div class="flex flex-col gap-4 py-8 text-[11px] text-neutral-500 md:flex-row md:items-center md:justify-between">
            <x-logo />
            <div class="flex gap-5"><a href="#top">Home</a><a href="#overview">Overview</a><a href="#features">Features</a><a href="#hardware">Hardware</a></div>
            <p>© {{ date('Y') }} LightSense. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
    document.getElementById('menu-btn').addEventListener('click', () => {
        const m = document.getElementById('mobile-menu');
        m.classList.toggle('hidden'); m.classList.toggle('flex');
    });
    document.querySelectorAll('#mobile-menu a').forEach(a => a.addEventListener('click', () => {
        const m = document.getElementById('mobile-menu');
        m.classList.add('hidden'); m.classList.remove('flex');
    }));
</script>
</x-layouts.base>
