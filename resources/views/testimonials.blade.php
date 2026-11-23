@php
    $input = 'w-full rounded-sm border border-line bg-ink/60 px-3 py-2.5 text-sm text-white placeholder:text-stone-600 focus:border-brass focus:outline-none [color-scheme:dark]';
    $lbl = 'mb-1.5 block text-xs font-semibold text-stone-300';
@endphp
<x-layouts.site title="Testimonials" description="What people say about using LightSense.">
<div class="mx-auto max-w-6xl px-4 py-10 sm:px-5 sm:py-16">
    <p class="eyebrow">Testimonials</p>
    <h1 class="mt-3 max-w-xl text-3xl font-bold leading-tight tracking-tight sm:text-4xl">What people say about LightSense</h1>

    @forelse ($testimonials as $t)
        @if ($loop->first) <div class="mt-10 grid gap-x-10 gap-y-8 border-t border-line pt-8 md:grid-cols-2"> @endif
            <figure>
                <blockquote class="break-words text-base leading-relaxed text-stone-200">&ldquo;{{ $t->quote }}&rdquo;</blockquote>
                <figcaption class="mt-3 text-xs text-stone-500">{{ $t->name }}@if ($t->role_title), {{ $t->role_title }}@endif</figcaption>
            </figure>
        @if ($loop->last) </div> @endif
    @empty
        <p class="mt-8 max-w-md border border-line bg-panel p-5 text-sm text-stone-400">No testimonials have been published yet. Be the first to share how LightSense worked for you.</p>
    @endforelse

    {{-- Share yours --}}
    <section class="mt-14 border border-line bg-panel p-5 sm:p-6 lg:max-w-2xl">
        <h2 class="text-xl font-bold">Share your experience</h2>
        @auth
            <p class="mt-1 text-xs text-stone-400">An administrator reads each testimonial before it is published.</p>
            <form method="POST" action="{{ route('testimonials.store') }}" class="mt-4 space-y-4" novalidate>
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="t-name" class="{{ $lbl }}">Name shown</label>
                        <input id="t-name" name="name" value="{{ old('name', auth()->user()->name) }}" required maxlength="60" class="{{ $input }}">
                        @error('name') <p class="mt-1 text-xs text-ember">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="t-role" class="{{ $lbl }}">Who you are (optional)</label>
                        <input id="t-role" name="role_title" value="{{ old('role_title') }}" placeholder="Student, teacher, homeowner" maxlength="60" class="{{ $input }}">
                    </div>
                </div>
                <div>
                    <label for="t-quote" class="{{ $lbl }}">Your testimonial</label>
                    <textarea id="t-quote" name="quote" rows="4" required minlength="20" maxlength="500" class="{{ $input }}">{{ old('quote') }}</textarea>
                    @error('quote') <p class="mt-1 text-xs text-ember">{{ $message }}</p> @enderror
                </div>
                <button class="btn btn-brass">Submit testimonial</button>
            </form>
        @else
            <p class="mt-2 text-sm text-stone-400">Log in to share your experience.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('login') }}" class="btn btn-brass">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-ghost">Create an account</a>
            </div>
        @endauth
    </section>
</div>
</x-layouts.site>
