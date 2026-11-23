@props(['device'])
{{-- Read-only OpenStreetMap map with a pin on the light. Drawn by public/js/maps.js --}}
<div {{ $attributes->merge(['class' => 'js-device-map isolate w-full rounded-sm border border-line bg-ink']) }}
     data-lat="{{ $device->latitude }}" data-lng="{{ $device->longitude }}"
     data-label="{{ $device->name }}, {{ $device->location }}"
     role="region" aria-label="Map showing where {{ $device->name }} is in {{ $device->location }}"></div>
