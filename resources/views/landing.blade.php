@php
    $steps = [
        ['Sense', 'A PIR sensor detects movement while an ambient light sensor measures how bright the room already is.'],
        ['Decide', 'The ESP32 weighs presence, brightness and the schedule from the real-time clock to decide if the lights should be on.'],
        ['Switch', 'A relay module turns the lights on or off, and the Wi-Fi module reports every change to your web dashboard.'],
    ];

    $features = [
        ['activity', 'Presence detection', 'Lights turn on only when someone is in the room and switch off when they leave.'],
        ['sun', 'Ambient light sensing', 'Skips switching on when daylight is already enough, so you save power.'],
        ['bolt', 'Energy monitoring', 'A PZEM-004T meter tracks voltage, current and consumption in real time.'],
        ['clock', 'Smart schedules', 'A DS3231 real-time clock keeps accurate time for schedule-based rules.'],
        ['wifi', 'Remote dashboard', 'Check status and usage from any browser over Wi-Fi.'],
        ['power', 'Relay control', 'A relay module safely switches one or several lighting circuits.'],
    ];

    $hardware = [
        ['Controller', 'ESP32', 'Runs the logic that ties every sensor and output together.'],
        ['Sensor', 'Ambient light sensor', 'Measures how bright the room is for smarter switching.'],
        ['Sensor', 'PIR HC-SR501', 'Detects human presence by sensing infrared motion.'],
        ['Metering', 'PZEM-004T', 'Reads voltage, current, power and energy use.'],
        ['Timing', 'DS3231 RTC', 'Keeps precise time so schedules stay accurate.'],
        ['Output', '5V relay module', 'Switches the lighting circuits on and off.'],
        ['Connectivity', 'ESP8266 ESP-01', 'Sends readings to the dashboard over Wi-Fi.'],
        ['Software', 'Web dashboard', 'Shows live status and stores readings in a database.'],
    ];
@endphp

<x-layouts.app title="LightSense | IoT Based Automatic Lighting System">

    {{-- Navigation --}}
    <header class="sticky top-0 z-40 border-b border-white/10 bg-ink/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5 md:px-8">
            <a href="{{ route('home') }}" aria-label="LightSense home"><x-logo /></a>

            <nav class="hidden items-center gap-8 text-sm md:flex" aria-label="Main">
                <a href="{{ route('home') }}" class="font-medium text-white">Home</a>
                <a href="#overview" class="text-white/60 transition hover:text-white">Overview</a>
                <a href="#features" class="text-white/60 transition hover:text-white">Features</a>
                <a href="#hardware" class="text-white/60 transition hover:text-white">Hardware</a>
            </nav>

            <div class="hidden items-center gap-5 md:flex">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-white/80 transition hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
                @endauth
            </div>

            <button type="button" data-menu-toggle="mobile-menu" aria-expanded="false" aria-label="Open menu"
                    class="grid size-10 place-items-center rounded-lg border border-white/15 text-white md:hidden">
                <x-icon name="menu" class="size-5" />
            </button>
        </div>

        <div id="mobile-menu" class="hidden border-t border-white/10 px-5 pb-5 pt-3 md:hidden">
            <nav class="flex flex-col text-sm" aria-label="Mobile">
                <a href="{{ route('home') }}" class="py-2.5 font-medium text-white">Home</a>
                <a href="#overview" class="py-2.5 text-white/70">Overview</a>
                <a href="#features" class="py-2.5 text-white/70">Features</a>
                <a href="#hardware" class="py-2.5 text-white/70">Hardware</a>
            </nav>
            <div class="mt-3 flex gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary flex-1">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline flex-1">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary flex-1">Get Started</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{-- Hero --}}
        <section class="bg-ink">
            <div class="mx-auto max-w-6xl px-5 py-14 md:px-8 md:py-24">
                <p class="mb-5 inline-flex rounded-full border border-brand/40 bg-brand/10 px-3 py-1 text-[11px] font-medium text-brand md:hidden">
                    IoT · ESP32 · Energy-aware lighting
                </p>

                <h1 class="text-4xl font-bold leading-[1.05] tracking-tight md:text-6xl">
                    IoT Based Automatic
                    <span class="block text-brand">Light System</span>
                </h1>

                <p class="mt-5 max-w-md text-sm leading-relaxed text-white/65 md:mt-6 md:text-base">
                    LightSense is an automatic light system that reads room brightness and presence, then switches your lights for you.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row md:mt-9">
                    <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="btn btn-primary">
                        {{ auth()->check() ? 'Open dashboard' : 'Get Started' }}
                    </a>
                    <a href="#overview" class="btn btn-outline">See how it works</a>
                </div>
            </div>
        </section>

        {{-- Overview --}}
        <section id="overview" class="scroll-mt-16 bg-white text-ink">
            <div class="mx-auto max-w-6xl px-5 py-14 md:px-8 md:py-20">
                <p class="eyebrow text-brand-deep">Overview</p>
                <h2 class="mt-3 text-2xl font-bold tracking-tight md:text-3xl">From movement to light in three steps</h2>
                <p class="mt-2 max-w-xl text-sm text-black/55">
                    LightSense reads the room, decides, and acts, with no wall switch needed.
                </p>

                <ol class="mt-10 grid gap-4 md:grid-cols-3">
                    @foreach ($steps as $i => [$title, $text])
                        <li class="rounded-2xl border border-black/5 bg-paper p-6">
                            <span class="grid size-7 place-items-center rounded-full bg-brand text-xs font-bold text-ink">{{ $i + 1 }}</span>
                            <h3 class="mt-5 text-base font-semibold">{{ $title }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-black/55">{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="scroll-mt-16 bg-night">
            <div class="mx-auto max-w-6xl px-5 py-14 md:px-8 md:py-20">
                <p class="eyebrow text-brand">Features</p>
                <h2 class="mt-3 max-w-md text-2xl font-bold tracking-tight md:max-w-none md:text-3xl">Everything your lighting needs to run itself</h2>
                <p class="mt-2 max-w-xl text-sm text-white/55">
                    Sensing, scheduling and monitoring in a single low-cost system.
                </p>

                <div class="mt-10 grid gap-4 md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                    @foreach ($features as [$icon, $title, $text])
                        <article class="rounded-2xl border border-white/10 bg-white/[0.03] p-5 md:rounded-none md:border-0 md:bg-transparent md:p-0">
                            <span class="grid size-8 place-items-center rounded-lg bg-brand/15 text-brand">
                                <x-icon :name="$icon" class="size-4" />
                            </span>
                            <h3 class="mt-4 text-sm font-semibold">{{ $title }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-white/55">{{ $text }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Hardware --}}
        <section id="hardware" class="scroll-mt-16 bg-white text-ink">
            <div class="mx-auto max-w-6xl px-5 py-14 md:px-8 md:py-20">
                <p class="eyebrow text-brand-deep">Hardware</p>
                <h2 class="mt-3 max-w-xs text-2xl font-bold tracking-tight md:max-w-none md:text-3xl">Built from affordable, proven components</h2>
                <p class="mt-2 max-w-xl text-sm text-black/55">
                    An ESP32-based build that is easy to source, wire and maintain.
                </p>

                <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($hardware as [$tag, $name, $text])
                        <article class="rounded-xl border border-black/5 bg-paper p-4">
                            <span class="inline-block rounded bg-brand-soft px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-brand-deep">{{ $tag }}</span>
                            <h3 class="mt-3 text-sm font-semibold">{{ $name }}</h3>
                            <p class="mt-1 text-xs leading-relaxed text-black/55">{{ $text }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Call to action --}}
        <section class="bg-ink">
            <div class="mx-auto max-w-6xl px-5 py-12 md:px-8 md:py-14">
                <div class="flex flex-col gap-6 rounded-3xl bg-brand p-6 text-ink md:flex-row md:items-center md:justify-between md:p-8">
                    <div>
                        <h2 class="text-xl font-bold tracking-tight md:text-2xl">Ready to stop flipping switches?</h2>
                        <p class="mt-1.5 text-sm text-ink/65">Set up LightSense and let your lights take care of themselves.</p>
                    </div>
                    <a href="{{ auth()->check() ? route('dashboard') : route('register') }}" class="btn btn-dark self-start md:self-auto">
                        {{ auth()->check() ? 'Open dashboard' : 'Get Started' }}
                    </a>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-ink">
        <div class="mx-auto flex max-w-6xl flex-col gap-5 px-5 pb-10 pt-4 text-xs text-white/50 md:flex-row md:items-center md:justify-between md:px-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:gap-10">
                <a href="{{ route('home') }}" aria-label="LightSense home"><x-logo /></a>
                <nav class="flex gap-6" aria-label="Footer">
                    <a href="{{ route('home') }}" class="hover:text-white">Home</a>
                    <a href="#overview" class="hover:text-white">Overview</a>
                    <a href="#features" class="hover:text-white">Features</a>
                    <a href="#hardware" class="hover:text-white">Hardware</a>
                </nav>
            </div>
            <p>&copy; {{ date('Y') }} LightSense. All rights reserved.</p>
        </div>
    </footer>
</x-layouts.app>
