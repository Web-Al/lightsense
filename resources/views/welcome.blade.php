<x-layouts.base title="IoT Based Automatic Light System">

{{-- Navigation bar: stays at the top while scrolling --}}
<div class="sticky top-0 z-50 border-b border-line bg-ink/90 backdrop-blur">
    <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-3">
        <x-logo />
        <div class="hidden items-center gap-6 text-xs text-stone-600 lg:flex">
            <a href="#top" class="font-semibold text-stone-900">Home</a>
            <a href="#overview" class="hover:text-stone-900">Overview</a>
            <a href="#features" class="hover:text-stone-900">Features</a>
            <a href="#hardware" class="hover:text-stone-900">Hardware</a>
        </div>
        <div class="hidden items-center gap-4 text-xs lg:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-brass !py-1.5 !text-xs">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-stone-700 hover:text-stone-900">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-brass !py-1.5 !text-xs">Get Started</a>
            @endauth
        </div>
        <button id="menu-btn" class="grid size-10 place-items-center rounded-sm border border-line lg:hidden" aria-label="Open menu" aria-controls="mobile-menu" aria-expanded="false">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </nav>
    <div id="mobile-menu" class="hidden flex-col gap-3 border-t border-line px-5 py-4 text-sm text-stone-700 lg:hidden">
        <a href="#overview">Overview</a><a href="#features">Features</a><a href="#hardware">Hardware</a>
        @auth <a href="{{ route('dashboard') }}">Dashboard</a> @else <a href="{{ route('login') }}">Log in</a> @endauth
    </div>
</div>

{{-- Hero --}}
<header class="hero-glow bg-ink">
    <div id="top" class="mx-auto grid max-w-6xl items-center gap-12 px-5 pb-20 pt-14 sm:pt-20 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
        <div>
            <p class="metric mb-4 text-xs text-brass">IoT / Arduino / energy-aware lighting</p>
            <h1 class="max-w-xl text-4xl font-bold leading-[1.05] tracking-tight sm:text-5xl">IoT Based Automatic <span class="text-brass">Light System</span></h1>
            <p class="mt-5 max-w-md text-sm leading-relaxed text-stone-600">LightSense is an automatic light system that reads room brightness and presence, then switches your lights for you.</p>
            <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="btn btn-brass">Get Started</a>
                <a href="#overview" class="btn btn-ghost">See how it works</a>
            </div>
        </div>

        {{-- Example of what the dashboard reads. Fixed sample, not live data. --}}
        <!-- <div class="border border-line bg-panel/80">
            <div class="flex items-center justify-between border-b border-line px-4 py-2.5">
                <span class="metric text-[11px] text-stone-600">living_room / ceiling_light</span>
                <span class="metric flex items-center gap-1.5 text-[11px] text-live"><span class="size-1.5 rounded-full bg-live pulse-live"></span>live</span>
            </div>
            <dl class="metric divide-y divide-line text-xs">
                @foreach ([['lux', '280', false], ['presence', 'detected', false], ['relay', 'ON', true], ['power', '34.2 W', true], ['voltage', '230 V', false]] as [$k, $v, $hot])
                    <div class="flex justify-between px-4 py-2.5"><dt class="text-stone-500">{{ $k }}</dt><dd class="{{ $hot ? 'text-live' : 'text-stone-800' }}">{{ $v }}</dd></div>
                @endforeach
            </dl>
            <p class="border-t border-line px-4 py-2 text-[10px] text-stone-500">Sample reading</p>
        </div> -->
    </div>
</header>


{{-- Overview --}}
<section id="overview" class="bg-paper py-20 text-stone-900">
    <div class="mx-auto max-w-6xl px-5">
        <p class="eyebrow !text-brass-dark">Overview</p>
        <h2 class="mt-3 max-w-xl text-3xl font-bold tracking-tight sm:text-4xl">From movement to light in three steps</h2>
        <p class="mt-3 text-sm text-stone-600">LightSense reads the room, decides, and acts. No wall switch needed.</p>

        <ol class="mt-10 grid border-t border-paper-line md:grid-cols-3 md:divide-x md:divide-paper-line">
            @foreach ($steps as $i => [$title, $text])
                <li class="border-b border-paper-line py-6 md:border-b-0 md:px-6 md:first:pl-0 md:last:pr-0">
                    <span class="metric text-xs text-brass-dark">0{{ $i + 1 }}</span>
                    <h3 class="mt-3 text-lg font-semibold">{{ $title }}</h3>
                    <p class="mt-2 max-w-xs text-xs leading-relaxed text-stone-600">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- Features: heading on the left, ruled list on the right --}}
<section id="features" class="bg-panel py-20">
    <div class="mx-auto grid max-w-6xl gap-10 px-5 lg:grid-cols-[minmax(0,5fr)_minmax(0,8fr)] lg:gap-16">
        <div class="lg:sticky lg:top-24 lg:self-start">
            <p class="eyebrow">Features</p>
            <h2 class="mt-3 text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Everything your lighting needs to run itself</h2>
            <p class="mt-3 max-w-sm text-sm text-stone-600">Sensing, scheduling and monitoring in a single low-cost system.</p>
        </div>

        <ul class="border-t border-line">
            @foreach ($features as [$title, $text, $icon])
                <li class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1.5 border-b border-line py-5 sm:grid-cols-[auto_11rem_1fr] sm:items-start sm:gap-x-6">
                    <svg class="size-6 text-brass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                    <h3 class="text-base font-semibold">{{ $title }}</h3>
                    <p class="col-span-2 text-xs leading-relaxed text-stone-600 sm:col-span-1">{{ $text }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>

{{-- Hardware: a parts list --}}
<section id="hardware" class="bg-paper py-20 text-stone-900">
    <div class="mx-auto max-w-6xl px-5">
        <p class="eyebrow !text-brass-dark">Hardware</p>
        <h2 class="mt-3 max-w-xl text-3xl font-bold tracking-tight sm:text-4xl">Built from affordable, proven components</h2>
        <p class="mt-3 text-sm text-stone-600">An Arduino-based build that is easy to source, wire and maintain.</p>

        <div class="mt-10 border-t border-stone-900">
            <div class="metric hidden grid-cols-[8rem_16rem_1fr] gap-x-4 border-b border-paper-line py-2 text-[11px] text-stone-500 md:grid">
                <span>Type</span><span>Component</span><span>What it does</span>
            </div>
            @foreach ($hardware as [$tag, $name, $text])
                <div class="grid grid-cols-[6.5rem_1fr] gap-x-4 gap-y-0.5 border-b border-paper-line py-3.5 md:grid-cols-[8rem_16rem_1fr] md:items-baseline">
                    <span class="metric text-[11px] text-brass-dark">{{ strtolower($tag) }}</span>
                    <span class="font-display text-sm font-semibold">{{ $name }}</span>
                    <span class="col-span-2 text-xs text-stone-600 md:col-span-1">{{ $text }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Call to action + footer --}}
<footer class="bg-ink">
    <div class="mx-auto max-w-6xl px-5 pt-16">
        <div class="hero-glow flex flex-col gap-6 border border-line bg-panel p-6 sm:flex-row sm:items-center sm:justify-between sm:p-10">
            <div class="max-w-md">
                <h2 class="text-2xl font-bold sm:text-3xl">Ready to stop flipping switches?</h2>
                <p class="mt-2 text-sm text-stone-600">Set up LightSense and let your lights take care of themselves.</p>
            </div>
            <a href="{{ route('register') }}" class="btn btn-brass self-start sm:self-auto">Get Started</a>
        </div>

        <div class="flex flex-col gap-4 py-8 text-[11px] text-stone-500 md:flex-row md:items-center md:justify-between">
            <x-logo />
            <div class="flex flex-wrap gap-x-5 gap-y-2"><a href="#top">Home</a><a href="#overview">Overview</a><a href="#features">Features</a><a href="#hardware">Hardware</a></div>
            <p class="metric">© {{ date('Y') }} LightSense</p>
        </div>
    </div>
</footer>

<script>
    document.getElementById('menu-btn').addEventListener('click', () => {
        const m = document.getElementById('mobile-menu');
        const open = m.classList.toggle('hidden') === false;
        m.classList.toggle('flex', open);
        document.getElementById('menu-btn').setAttribute('aria-expanded', String(open));
        document.getElementById('menu-btn').setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    });
    document.querySelectorAll('#mobile-menu a').forEach(a => a.addEventListener('click', () => {
        const m = document.getElementById('mobile-menu');
        m.classList.add('hidden'); m.classList.remove('flex');
        document.getElementById('menu-btn').setAttribute('aria-expanded', 'false');
        document.getElementById('menu-btn').setAttribute('aria-label', 'Open menu');
    }));
</script>
</x-layouts.base>
