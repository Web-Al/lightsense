@props(['lat' => null, 'lng' => null])
{{-- Click-to-place map used in the add and edit forms. Fills the hidden latitude and longitude fields. --}}
<div {{ $attributes->merge(['class' => 'js-map-picker']) }}>
    <div class="flex flex-wrap gap-2">
        <input type="search" data-search aria-label="Search for a place or address" placeholder="Search for a place or address"
            class="min-w-0 flex-1 rounded-md border border-line bg-ink/60 px-2.5 py-2 text-xs text-white placeholder:text-neutral-600 focus:border-amber focus:outline-none">
        <button type="button" data-find class="btn btn-ghost !px-3 !py-1.5 !text-xs">Search</button>
        <button type="button" data-locate class="btn btn-ghost !px-3 !py-1.5 !text-xs">Use my location</button>
    </div>

    <div data-map class="isolate mt-2 h-64 w-full rounded-md border border-line bg-ink"></div>

    <input type="hidden" name="latitude" value="{{ $lat }}">
    <input type="hidden" name="longitude" value="{{ $lng }}">

    <div class="mt-1.5 flex items-start justify-between gap-3">
        <p data-status aria-live="polite" class="text-[10px] text-neutral-500">Click the map or drag the pin to set where the light is.</p>
        <button type="button" data-clear class="shrink-0 text-[10px] font-semibold text-neutral-400 hover:text-white">Remove position</button>
    </div>
</div>
