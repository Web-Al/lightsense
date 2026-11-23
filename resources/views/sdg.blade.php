@php $input = 'w-full rounded-sm border border-line bg-ink/60 px-2.5 py-2 text-sm text-white focus:border-brass focus:outline-none [color-scheme:dark]'; @endphp
<x-layouts.site title="SDG 7: Affordable and Clean Energy" description="What UN Sustainable Development Goal 7 asks for, where the world stands, and how smarter lighting helps. Figures are sourced and linked.">

<header class="hero-glow border-b border-line">
    <div class="mx-auto max-w-6xl px-5 pb-14 pt-14 sm:pt-20">
        <p class="metric text-xs text-brass">Sustainable Development Goal 7</p>
        <h1 class="mt-3 max-w-2xl text-3xl font-bold leading-[1.05] tracking-tight sm:text-4xl lg:text-5xl">Affordable and clean energy for everyone</h1>
        <p class="mt-5 max-w-xl text-sm leading-relaxed text-stone-400">SDG 7 calls for access to affordable, reliable, sustainable and modern energy for all. Its third target, 7.3, asks the world to double the rate at which it improves energy efficiency by 2030. Using less energy for the same result is the cheapest energy there is.</p>
        <p class="metric mt-4 text-[11px] text-stone-500">Facts checked on 13 November 2026</p>
    </div>
</header>

{{-- Where the world stands --}}
<section class="mx-auto max-w-6xl px-5 py-16">
    <p class="eyebrow">Where the world stands</p>
    <h2 class="mt-3 max-w-xl text-3xl font-bold tracking-tight">Progress is real, but it is slowing down</h2>

    <dl class="mt-8 grid gap-px border border-line bg-line md:grid-cols-3">
        <div class="bg-panel p-5">
            <dd class="metric text-3xl font-medium lg:text-4xl">655 million</dd>
            <dt class="mt-2 text-xs leading-relaxed text-stone-400">people still live without electricity, according to the 2026 Energy Progress Report. <a href="#sources" class="text-brass">[1]</a></dt>
        </div>
        <div class="bg-panel p-5">
            <dd class="metric text-4xl font-medium">1.5%</dd>
            <dt class="mt-2 text-xs leading-relaxed text-stone-400">is how much global energy intensity improved in 2023, down from 2.4% in 2022. <a href="#sources" class="text-brass">[2]</a></dt>
        </div>
        <div class="bg-panel p-5">
            <dd class="metric text-4xl font-medium">about 4%</dd>
            <dt class="mt-2 text-xs leading-relaxed text-stone-400">improvement every year through 2030 is now needed to reach the efficiency target. <a href="#sources" class="text-brass">[2]</a></dt>
        </div>
    </dl>

    {{-- Infographic: bars drawn with HTML, described for screen readers --}}
    <figure class="mt-10 border border-line bg-panel p-5">
        <figcaption class="text-sm font-semibold">Yearly improvement in global energy efficiency</figcaption>
        <div role="img" aria-label="Bar chart. Global energy efficiency improved 2.4 percent in 2022 and 1.5 percent in 2023. About 4 percent a year is needed through 2030." class="mt-4">
            @foreach ([['2022', 2.4, '2.4%', false], ['2023', 1.5, '1.5%', false], ['Needed each year to 2030', 4, 'about 4%', true]] as [$label, $value, $text, $needed])
                <div class="grid grid-cols-[minmax(0,1fr)_3.5rem] gap-x-3 gap-y-1 py-2 sm:grid-cols-[12rem_minmax(0,1fr)_5rem] sm:items-center sm:gap-3 sm:py-1.5">
                    <span class="col-span-2 text-xs text-stone-400 sm:col-span-1 sm:col-start-1 sm:row-start-1">{{ $label }}</span>
                    <div class="h-5 min-w-0 bg-white/5 sm:col-start-2 sm:row-start-1"><div class="h-5 {{ $needed ? 'border border-dashed border-brass bg-brass/10' : 'bg-gradient-to-r from-brass/20 to-brass/70' }}" style="width: {{ ($value / 4) * 100 }}%"></div></div>
                    <span class="metric col-start-2 row-start-2 text-right text-xs sm:col-start-3 sm:row-start-1">{{ $text }}</span>
                </div>
            @endforeach
        </div>
        <p class="mt-3 text-[11px] text-stone-500">Source: Tracking SDG 7, Energy Progress Report 2026. <a href="#sources" class="text-brass">[2]</a></p>
    </figure>
</section>

{{-- Philippines + lighting --}}
<section class="border-y border-line bg-panel">
    <div class="mx-auto grid max-w-6xl gap-10 px-5 py-16 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:gap-16">
        <div>
            <p class="eyebrow">Why lighting matters here</p>
            <h2 class="mt-3 text-3xl font-bold leading-tight tracking-tight">Lighting is a big share of the electricity we use</h2>
        </div>
        <ul class="border-t border-line text-sm leading-relaxed text-stone-300">
            <li class="border-b border-line py-4">An Asian Development Bank case study estimated that lighting in Philippine buildings and homes makes up roughly 30% to 40% of the country's electricity use. This is an older estimate, published in 2015, so treat it as a rough guide. <a href="#sources" class="text-brass">[3]</a></li>
            <li class="border-b border-line py-4">The Department of Energy's Government Energy Management Program aims to cut electricity and fuel use in government offices by at least 10%. <a href="#sources" class="text-brass">[4]</a></li>
            <li class="border-b border-line py-4">The measures it recommends include turning off equipment when it is not in use, switching to LED lighting, and making the most of natural light. <a href="#sources" class="text-brass">[5]</a></li>
        </ul>
    </div>
</section>

{{-- How LightSense helps --}}
<section class="mx-auto max-w-6xl px-5 py-16">
    <p class="eyebrow">How LightSense helps</p>
    <h2 class="mt-3 max-w-xl text-3xl font-bold tracking-tight">It automates the habits that already save energy</h2>
    <ul class="mt-8 border-t border-line">
        @foreach ([
            ['Presence sensing', 'A motion sensor lets the light run only while someone is in the room. This is the "turn off when not in use" rule, done automatically.'],
            ['Daylight sensing', 'A light sensor skips switching on when the room is already bright. This is the "use natural light" rule.'],
            ['Schedules', 'You choose the hours when lights may turn on, so corridors and shared rooms are not lit all night.'],
            ['Energy monitoring', 'An energy meter reports power and kilowatt-hours to the dashboard, so savings are measured instead of guessed.'],
        ] as [$title, $text])
            <li class="grid gap-1 border-b border-line py-4 sm:grid-cols-[14rem_1fr] sm:gap-6">
                <h3 class="font-semibold">{{ $title }}</h3>
                <p class="text-sm text-stone-400">{{ $text }}</p>
            </li>
        @endforeach
    </ul>
</section>

{{-- Savings estimator --}}
<section class="border-y border-line bg-panel">
    <div class="mx-auto grid max-w-6xl gap-10 px-5 py-16 lg:grid-cols-2 lg:gap-16">
        <div>
            <p class="eyebrow">Try it</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight">Estimate what wasted light costs you</h2>
            <p class="mt-3 max-w-md text-sm text-stone-400">Enter your own numbers. The result is a simple estimate of the energy you could save if lights were off whenever a room is empty or bright. It is not a measured result.</p>
        </div>

        <div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label for="e-lights" class="mb-1 block text-xs font-semibold text-stone-300">Number of lights</label>
                    <input id="e-lights" type="number" min="1" max="1000" value="10" class="{{ $input }}"></div>
                <div><label for="e-watts" class="mb-1 block text-xs font-semibold text-stone-300">Watts per light</label>
                    <input id="e-watts" type="number" min="1" max="500" value="9" class="{{ $input }}"></div>
                <div><label for="e-hours" class="mb-1 block text-xs font-semibold text-stone-300">Wasted hours per day</label>
                    <input id="e-hours" type="number" min="0" max="24" step="0.5" value="3" class="{{ $input }}"></div>
                <div><label for="e-rate" class="mb-1 block text-xs font-semibold text-stone-300">Price per kWh (₱)</label>
                    <input id="e-rate" type="number" min="0" step="0.1" value="12" class="{{ $input }}"></div>
            </div>
            <p class="mt-2 text-[11px] text-stone-500">"Wasted hours" means hours a light stays on with nobody in the room, or in bright daylight. Check your own bill for the price.</p>

            <div class="mt-5 grid gap-px border border-line bg-line sm:grid-cols-2" aria-live="polite">
                <div class="bg-ink p-4"><p class="text-xs text-stone-400">Energy wasted per year</p><p id="e-kwh" class="metric mt-1 text-2xl font-medium">0 kWh</p></div>
                <div class="bg-ink p-4"><p class="text-xs text-stone-400">Cost per year</p><p id="e-cost" class="metric mt-1 text-2xl font-medium">₱0</p></div>
            </div>
        </div>
    </div>
</section>

{{-- Take action --}}
<section class="mx-auto max-w-6xl px-5 py-16">
    <p class="eyebrow">Take action</p>
    <h2 class="mt-3 max-w-xl text-3xl font-bold tracking-tight">Five things you can do this week</h2>
    <ol class="mt-8 border-t border-line">
        @foreach ([
            'Switch lights off when you leave a room.',
            'Open the curtains and use daylight before you switch a light on.',
            'Replace old bulbs with LED bulbs.',
            'Read your electricity bill every month and note the kilowatt-hours.',
            'Tell someone about SDG 7 and share this page.',
        ] as $i => $action)
            <li class="grid grid-cols-[3rem_1fr] items-baseline border-b border-line py-4 text-sm">
                <span class="metric text-xs text-brass">0{{ $i + 1 }}</span><span>{{ $action }}</span>
            </li>
        @endforeach
    </ol>
    <div class="mt-8 flex flex-wrap gap-3">
        <button type="button" id="share-btn" class="btn btn-brass">Share this page</button>
        @guest <a href="{{ route('register') }}" class="btn btn-ghost">Create an account</a> @endguest
        <a href="{{ route('contact') }}" class="btn btn-ghost">Ask us a question</a>
    </div>
</section>

{{-- Sources --}}
<section id="sources" class="border-t border-line bg-panel">
    <div class="mx-auto max-w-6xl px-5 py-14">
        <h2 class="text-xl font-bold">Sources</h2>
        <ol class="mt-4 space-y-3 text-xs leading-relaxed text-stone-400">
            <li>[1] IEA, IRENA, UNSD, World Bank and WHO. 2026. <a class="text-brass underline" href="https://trackingsdg7.esmap.org/sites/default/files/download-documents/SDG7-Report2026-0623-V4-4Web.pdf" rel="noopener">Tracking SDG 7: The Energy Progress Report 2026</a>. The 655 million figure was reported in the report's June 2026 press release.</li>
            <li>[2] Sustainable Energy for All. 2026. <a class="text-brass underline" href="https://www.seforall.org/system/files/2026-07/SEforALL%20Tracking%20SDG7%20-%20The%20Energy%20Progress%20Report%202026_Executive%20Version_22Jul2026-2.pdf" rel="noopener">Tracking SDG 7: Energy Progress Report 2026, Executive Version</a>. The 2.4% figure for 2022 is from <a class="text-brass underline" href="https://sdg.iisd.org/news/2026-energy-progress-report-finds-renewables-supply-over-30-of-electricity/" rel="noopener">IISD SDG Knowledge Hub</a>.</li>
            <li>[3] Asian Development Bank. <a class="text-brass underline" href="https://events.development.asia/sites/default/files/materials/2015/10/philippine-energy-efficiency-project-lighting%C2%A0-philippines-efficient%C2%A0way.pdf" rel="noopener">The Philippine Energy Efficiency Project: Lighting Up the Philippines in an Efficient Way</a> (published 2015).</li>
            <li>[4] Department of Energy, Philippines. <a class="text-brass underline" href="https://doe.gov.ph/node/25337" rel="noopener">Government Energy Management Program update</a>.</li>
            <li>[5] Department of Energy, Philippines. <a class="text-brass underline" href="https://legacy.doe.gov.ph/sites/default/files/pdf/announcements/iloilo-energy-101-06-efficiency-101.pdf" rel="noopener">Energy efficiency briefing slides</a>, list of common energy efficiency and conservation measures.</li>
        </ol>
        <p class="mt-4 text-[11px] text-stone-500">Please check figures against the sources before you quote them. Reports are updated every year.</p>
    </div>
</section>

<x-slot:scripts>
<script>
    // Savings estimator
    const ids = ['e-lights', 'e-watts', 'e-hours', 'e-rate'];
    function estimate() {
        const [lights, watts, hours, rate] = ids.map(id => Math.max(0, parseFloat(document.getElementById(id).value) || 0));
        const kwh = lights * watts / 1000 * hours * 365;
        document.getElementById('e-kwh').textContent = kwh.toLocaleString(undefined, { maximumFractionDigits: 0 }) + ' kWh';
        document.getElementById('e-cost').textContent = '₱' + (kwh * rate).toLocaleString(undefined, { maximumFractionDigits: 0 });
    }
    ids.forEach(id => document.getElementById(id).addEventListener('input', estimate));
    estimate();

    // Share button
    document.getElementById('share-btn').addEventListener('click', async () => {
        const data = { title: document.title, url: location.href };
        try {
            if (navigator.share) {
                await navigator.share(data);
                window.LightSenseToast?.show({ type: 'success', message: 'Thanks for sharing!' });
            } else {
                await navigator.clipboard.writeText(location.href);
                window.LightSenseToast?.show({ type: 'success', message: 'Link copied. Paste it anywhere to share.' });
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.LightSenseToast?.show({ type: 'error', message: 'Could not share this page. Please try again.' });
            }
        }
    });
</script>
</x-slot:scripts>
</x-layouts.site>
