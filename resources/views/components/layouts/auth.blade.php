@props(['title', 'badge', 'heading', 'accent', 'blurb'])
<x-layouts.base :title="$title">
<div class="min-h-screen lg:grid lg:grid-cols-[1fr_1.1fr]">
    {{-- Brand panel: left on desktop, header on mobile --}}
    <aside class="relative flex flex-col justify-between overflow-hidden bg-ink p-6 sm:p-8 lg:p-10">
        <div class="pointer-events-none absolute -bottom-24 -left-10 hidden size-96 rounded-full bg-amber/30 blur-3xl lg:block"></div>
        <x-logo />
        <div class="relative my-10 max-w-sm lg:my-0">
            <span class="mb-4 inline-block rounded-full border border-amber/40 bg-amber/10 px-2.5 py-0.5 text-[10px] font-semibold text-amber">{{ $badge }}</span>
            <h1 class="text-3xl font-extrabold leading-tight tracking-tight lg:text-4xl">{{ $heading }}<br><span class="text-amber">{{ $accent }}</span></h1>
            <p class="mt-4 text-sm text-neutral-400">{{ $blurb }}</p>
        </div>
        <p class="relative hidden text-[11px] text-neutral-500 lg:block">© {{ date('Y') }} LightSense. All rights reserved.</p>
    </aside>

    <main class="-mt-4 rounded-t-2xl bg-white px-6 py-8 text-neutral-900 sm:px-10 lg:mt-0 lg:grid lg:place-items-center lg:rounded-none">
        <div class="mx-auto w-full max-w-sm">{{ $slot }}</div>
    </main>
</div>
<script>
    document.querySelectorAll('[data-toggle-password]').forEach(btn => btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.togglePassword);
        input.type = input.type === 'password' ? 'text' : 'password';
    }));
</script>
</x-layouts.base>
