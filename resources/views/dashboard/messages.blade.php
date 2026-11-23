<x-layouts.dashboard title="Messages" heading="Messages" sub="Messages sent from the contact form">
    <section class="mt-4 border border-line bg-panel">
        @forelse ($messages as $m)
            <article class="border-b border-line p-4 last:border-0 {{ $m->read_at ? '' : 'glow-live' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="break-words text-sm font-semibold">
                            @unless ($m->read_at) <span class="metric mr-1.5 text-[10px] text-live">NEW</span> @endunless{{ $m->subject }}
                        </h2>
                        <p class="metric break-words text-[11px] text-stone-500">{{ $m->name }} &middot; <a href="mailto:{{ $m->email }}" class="break-all text-brass">{{ $m->email }}</a> &middot; {{ $m->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-semibold">
                        <form method="POST" action="{{ route('messages.read', $m) }}">@csrf @method('PATCH')
                            <button class="min-h-10 px-1 text-left text-stone-400 hover:text-stone-900">{{ $m->read_at ? 'Mark as unread' : 'Mark as read' }}</button></form>
                        <form method="POST" action="{{ route('messages.destroy', $m) }}" onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')
                            <button class="min-h-10 px-1 text-left text-stone-500 hover:text-ember">Delete</button></form>
                    </div>
                </div>
                <p class="mt-3 max-w-2xl break-words whitespace-pre-line text-xs leading-relaxed text-stone-300">{{ $m->message }}</p>
            </article>
        @empty
            <p class="p-4 text-xs text-stone-400">No messages yet.</p>
        @endforelse
    </section>
</x-layouts.dashboard>
