@props(['lat' => null, 'lng' => null])
{{-- Click-to-place map used in the add and edit forms. Fills the hidden latitude and longitude fields. --}}
<div {{ $attributes->merge(['class' => 'js-map-picker']) }}>
    <div class="grid grid-cols-2 gap-2 sm:flex">
        <input type="search" data-search aria-label="Search for a place or address" placeholder="Search for a place or address"
            class="col-span-2 min-w-0 rounded-sm border border-line bg-ink/60 px-2.5 py-2 text-xs text-stone-900 placeholder:text-stone-600 focus:border-brass focus:outline-none sm:col-span-1 sm:flex-1">
        <button type="button" data-find class="btn btn-ghost whitespace-nowrap !px-3 !py-1.5 !text-xs">Search</button>
        <button type="button" data-locate class="btn btn-ghost whitespace-nowrap !px-3 !py-1.5 !text-xs">Use my location</button>
    </div>

    <div data-map class="isolate mt-2 h-64 w-full rounded-sm border border-line bg-ink"></div>

    <input type="hidden" name="latitude" value="{{ $lat }}">
    <input type="hidden" name="longitude" value="{{ $lng }}">

    <div class="mt-1.5 flex flex-wrap items-start justify-between gap-2">
        <p data-status aria-live="polite" class="min-w-0 flex-1 text-[10px] text-stone-500">Click the map or drag the pin to set where the light is.</p>
        <button type="button" data-clear class="min-h-10 shrink-0 px-2 text-[10px] font-semibold text-stone-600 hover:text-stone-900">Remove position</button>
    </div>
</div>
