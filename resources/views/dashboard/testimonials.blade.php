@php $lists = [['Waiting for approval', $pending, false], ['Published', $published, true]]; @endphp
<x-layouts.dashboard title="Testimonials" heading="Testimonials" sub="Approve what appears on the public site">
    @foreach ($lists as [$title, $items, $isPublished])
        <section class="mt-4 border border-line bg-panel p-4">
            <h2 class="text-[11px] font-semibold">{{ $title }} ({{ $items->count() }})</h2>
            @forelse ($items as $t)
                <article class="mt-3 flex flex-wrap items-start justify-between gap-3 border-t border-line pt-3">
                    <div class="min-w-0 max-w-2xl">
                        <p class="break-words text-xs leading-relaxed text-stone-200">&ldquo;{{ $t->quote }}&rdquo;</p>
                        <p class="metric mt-1 text-[10px] text-stone-500">{{ $t->name }}@if ($t->role_title), {{ $t->role_title }}@endif &middot; {{ $t->created_at->format('M j, Y') }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-semibold">
                        <form method="POST" action="{{ route('testimonials.toggle', $t) }}">@csrf @method('PATCH')
                            <button class="min-h-10 px-1 text-left {{ $isPublished ? 'text-stone-400 hover:text-stone-900' : 'text-brass' }}">{{ $isPublished ? 'Hide' : 'Approve and publish' }}</button></form>
                        <form method="POST" action="{{ route('testimonials.destroy', $t) }}" onsubmit="return confirm('Delete this testimonial?')">@csrf @method('DELETE')
                            <button class="min-h-10 px-1 text-left text-stone-500 hover:text-ember">Delete</button></form>
                    </div>
                </article>
            @empty
                <p class="mt-3 text-xs text-stone-500">Nothing here.</p>
            @endforelse
        </section>
    @endforeach
</x-layouts.dashboard>
