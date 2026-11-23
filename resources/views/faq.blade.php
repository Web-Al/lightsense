<x-layouts.site title="FAQ" description="Answers to common questions about LightSense and how it supports SDG 7.">
<div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:gap-10 sm:px-5 sm:py-16 lg:grid-cols-[minmax(0,4fr)_minmax(0,8fr)] lg:gap-16">
    <div>
        <p class="eyebrow">FAQ</p>
        <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Questions, answered</h1>
        <p class="mt-3 max-w-xs text-sm text-stone-400">Can't find yours? <a href="{{ route('contact') }}" class="font-semibold text-brass">Send us a message</a>.</p>
    </div>

    <div class="border-t border-line">
        @foreach ($faqs as [$q, $a])
            <details class="group border-b border-line py-4" @if ($loop->first) open @endif>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-display text-base font-semibold [&::-webkit-details-marker]:hidden">
                    <span class="min-w-0 break-words">{{ $q }}</span>
                    <span class="metric text-brass group-open:hidden" aria-hidden="true">+</span>
                    <span class="metric hidden text-brass group-open:inline" aria-hidden="true">&minus;</span>
                </summary>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-stone-400">{{ $a }}</p>
            </details>
        @endforeach
    </div>
</div>
</x-layouts.site>
