@props(['name', 'label', 'placeholder' => '••••••••', 'hint' => null, 'link' => null])
<div>
    <div class="flex items-center justify-between">
        <label for="{{ $name }}" class="label">{{ $label }}</label>
        @if ($link) <a href="{{ $link['href'] }}" class="mb-1.5 text-xs font-semibold text-brass-dark">{{ $link['text'] }}</a> @endif
    </div>
    <div class="relative">
        <input id="{{ $name }}" name="{{ $name }}" type="password" placeholder="{{ $placeholder }}" required class="field pr-10">
        <button type="button" data-toggle-password="{{ $name }}" aria-label="Show or hide password"
            class="absolute inset-y-0 right-3 text-stone-500 hover:text-stone-900">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
    </div>
    @if ($hint) <p class="mt-1.5 text-[11px] text-stone-500">{{ $hint }}</p> @endif
    @error($name) <p class="mt-1 text-xs text-ember-dark">{{ $message }}</p> @enderror
</div>
