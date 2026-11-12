@php
    $input = 'rounded-md border border-line bg-ink/60 px-2.5 py-1.5 text-xs text-white focus:border-amber focus:outline-none [color-scheme:dark]';
    $descriptions = [
        'admin' => 'Can do everything, including changing other people\'s roles.',
        'staff' => 'Can use the light controls and add, turn on or off, and delete schedules.',
        'viewer' => 'Can look at every page but cannot change anything.',
    ];
@endphp
<x-layouts.dashboard title="Users" heading="Users" sub="Choose what each person is allowed to do">

    @if ($errors->any())
        <ul role="alert" class="mt-3 space-y-1 rounded-md border border-red-500/40 bg-red-500/10 px-3 py-2 text-xs text-red-400">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    @endif

    {{-- What each role can do --}}
    <section class="mt-4 grid gap-3 md:grid-cols-3">
        @foreach (\App\Models\User::ROLES as $key => $label)
            <div class="rounded-lg border border-line bg-panel-2 p-3">
                <p class="text-xs font-bold">{{ $label }}</p>
                <p class="mt-1 text-[11px] leading-relaxed text-neutral-400">{{ $descriptions[$key] }}</p>
            </div>
        @endforeach
    </section>

    {{-- People --}}
    <section class="mt-3 rounded-lg border border-line bg-panel-2 p-4">
        <h2 class="text-[11px] font-semibold">People ({{ $users->count() }})</h2>

        @foreach ($users as $user)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line py-3 last:border-0">
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold">{{ $user->name }} @if ($user->is(auth()->user())) <span class="font-normal text-neutral-500">(you)</span> @endif</p>
                    <p class="truncate text-[10px] text-neutral-500">{{ $user->email }} · joined {{ $user->created_at->format('M j, Y') }}</p>
                </div>

                @if ($user->is(auth()->user()))
                    <span class="rounded-full bg-amber/15 px-2.5 py-1 text-[11px] font-semibold text-amber">{{ $user->role_label }}</span>
                @else
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('users.update', $user) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <label for="role-{{ $user->id }}" class="sr-only">Role for {{ $user->name }}</label>
                            <select id="role-{{ $user->id }}" name="role" class="{{ $input }}">
                                @foreach (\App\Models\User::ROLES as $key => $label)
                                    <option value="{{ $key }}" @selected($user->role === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-amber !px-3 !py-1.5 !text-xs">Save role</button>
                        </form>
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button class="text-neutral-500 hover:text-red-400" aria-label="Delete {{ $user->name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    </section>
</x-layouts.dashboard>
