@php
    $input = 'w-full rounded-sm border border-line bg-panel px-3 py-2.5 text-sm text-white placeholder:text-stone-600 focus:border-brass focus:outline-none [color-scheme:dark]';
    $lbl = 'mb-1.5 block text-xs font-semibold text-stone-300';
@endphp
<x-layouts.site title="Contact" description="Send a question or feedback to the LightSense team.">
<div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:gap-10 sm:px-5 sm:py-16 lg:grid-cols-[minmax(0,4fr)_minmax(0,8fr)] lg:gap-16">
    <div>
        <p class="eyebrow">Contact</p>
        <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Talk to us</h1>
        <p class="mt-3 max-w-xs text-sm text-stone-400">Ask a question, report a problem, or tell us how LightSense could be better. An administrator reads every message.</p>
        <p class="mt-4 max-w-xs text-xs text-stone-500">You may also find your answer on the <a href="{{ route('faq') }}" class="text-brass">FAQ page</a>.</p>
    </div>

    <form method="POST" action="{{ route('contact.store') }}" class="space-y-4 border border-line bg-panel p-5 sm:p-6" novalidate>
        @csrf
        {{-- Spam trap: hidden from people --}}
        <div class="hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="name" class="{{ $lbl }}">Your name</label>
                <input id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required maxlength="80" autocomplete="name" class="{{ $input }}">
                @error('name') <p class="mt-1 text-xs text-ember">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="{{ $lbl }}">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email ?? '') }}" required maxlength="120" autocomplete="email" class="{{ $input }}">
                @error('email') <p class="mt-1 text-xs text-ember">{{ $message }}</p> @enderror
            </div>
        </div>
        <div>
            <label for="subject" class="{{ $lbl }}">Subject</label>
            <input id="subject" name="subject" value="{{ old('subject') }}" required maxlength="120" class="{{ $input }}">
            @error('subject') <p class="mt-1 text-xs text-ember">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="message" class="{{ $lbl }}">Message</label>
            <textarea id="message" name="message" rows="6" required minlength="10" maxlength="2000" class="{{ $input }}">{{ old('message') }}</textarea>
            @error('message') <p class="mt-1 text-xs text-ember">{{ $message }}</p> @enderror
        </div>
        <button class="btn btn-brass">Send message</button>
    </form>
</div>
</x-layouts.site>
