@php
    $input = 'w-full rounded-md border border-line bg-ink/60 px-2.5 py-2 text-xs text-white placeholder:text-neutral-600 focus:border-amber focus:outline-none [color-scheme:dark]';
@endphp
<x-layouts.dashboard title="Schedules" heading="Schedules" sub="Choose when the lights are allowed to turn on">

    @php $canEdit = auth()->user()->isStaff(); @endphp

    @unless ($canEdit)
        <p class="mt-4 rounded-md border border-line bg-panel-2 px-3 py-2.5 text-xs text-neutral-400">You have view-only access. Ask an administrator for Staff access to add, turn on or off, or delete schedules.</p>
    @endunless

    {{-- Add schedule --}}
    @if ($canEdit)
    <section class="mt-4 rounded-lg border border-line bg-panel-2 p-4">
        <h2 class="text-[11px] font-semibold">Add a schedule</h2>
        <form method="POST" action="{{ route('schedules.store') }}" class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr_auto] lg:items-end">
            @csrf
            <div>
                <label for="name" class="mb-1 block text-[10px] font-semibold text-neutral-400">Name</label>
                <input id="name" name="name" value="{{ old('name') }}" placeholder="Evening lights" maxlength="60" required class="{{ $input }}">
            </div>
            <div>
                <label for="days" class="mb-1 block text-[10px] font-semibold text-neutral-400">Days</label>
                <select id="days" name="days" class="{{ $input }}">
                    @foreach (\App\Models\Schedule::DAYS as $value => $label)
                        <option value="{{ $value }}" @selected(old('days') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="start_time" class="mb-1 block text-[10px] font-semibold text-neutral-400">Starts</label>
                <input id="start_time" name="start_time" type="time" value="{{ old('start_time', '18:00') }}" required class="{{ $input }}">
            </div>
            <div>
                <label for="end_time" class="mb-1 block text-[10px] font-semibold text-neutral-400">Ends (optional)</label>
                <input id="end_time" name="end_time" type="time" value="{{ old('end_time') }}" class="{{ $input }}">
            </div>
            <button class="btn btn-amber sm:col-span-2 lg:col-span-1">Add schedule</button>
        </form>
        @if ($errors->any())
            <ul class="mt-3 space-y-1 text-xs text-red-400">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        @endif
    </section>
    @endif

    {{-- List --}}
    <section class="mt-3 rounded-lg border border-line bg-panel-2 p-4">
        <h2 class="text-[11px] font-semibold">Your schedules</h2>

        @forelse ($schedules as $schedule)
            <div class="flex items-center justify-between gap-3 border-b border-line py-3 last:border-0">
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold {{ $schedule->enabled ? '' : 'text-neutral-500' }}">{{ $schedule->name }}</p>
                    <p class="text-[10px] text-neutral-500">{{ $schedule->summary }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <x-schedule-switch :schedule="$schedule" />
                    @if ($canEdit)
                    <form method="POST" action="{{ route('schedules.destroy', $schedule) }}" onsubmit="return confirm('Delete {{ addslashes($schedule->name) }}?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-neutral-500 hover:text-red-400" aria-label="Delete {{ $schedule->name }}">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="mt-3 text-xs text-neutral-500">{{ $canEdit ? 'You have no schedules yet. Use the form above to add one, for example "Evening lights" from 6:00 PM to 11:00 PM.' : 'There are no schedules yet.' }}</p>
        @endforelse
    </section>
</x-layouts.dashboard>
