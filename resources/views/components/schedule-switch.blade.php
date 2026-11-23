@props(['schedule'])
@if (auth()->user()->isStaff())
    <form method="POST" action="{{ route('schedules.toggle', $schedule) }}">
        @csrf
        @method('PATCH')
        <button type="submit" role="switch" aria-checked="{{ $schedule->enabled ? 'true' : 'false' }}" aria-label="Turn {{ $schedule->name }} {{ $schedule->enabled ? 'off' : 'on' }}"
            class="group relative grid size-11 shrink-0 place-items-center rounded-sm focus-visible:outline">
            <span class="relative h-5 w-9 rounded-sm transition {{ $schedule->enabled ? 'bg-brass' : 'bg-line' }}">
                <span class="absolute left-0.5 top-0.5 size-4 rounded-sm bg-white shadow transition-transform {{ $schedule->enabled ? 'translate-x-4' : '' }}"></span>
            </span>
        </button>
    </form>
@else
    {{-- Viewers see the state but cannot change it --}}
    <span class="text-[10px] font-semibold {{ $schedule->enabled ? 'text-brass' : 'text-stone-500' }}">{{ $schedule->enabled ? 'On' : 'Off' }}</span>
@endif
