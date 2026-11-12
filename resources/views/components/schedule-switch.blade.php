@props(['schedule'])
@if (auth()->user()->isStaff())
    <form method="POST" action="{{ route('schedules.toggle', $schedule) }}">
        @csrf
        @method('PATCH')
        <button type="submit" role="switch" aria-checked="{{ $schedule->enabled ? 'true' : 'false' }}" aria-label="Turn {{ $schedule->name }} {{ $schedule->enabled ? 'off' : 'on' }}"
            class="relative block h-4 w-7 rounded-full transition {{ $schedule->enabled ? 'bg-amber' : 'bg-line' }}">
            <span class="absolute top-0.5 size-3 rounded-full bg-white transition-all {{ $schedule->enabled ? 'left-3.5' : 'left-0.5' }}"></span>
        </button>
    </form>
@else
    {{-- Viewers see the state but cannot change it --}}
    <span class="text-[10px] font-semibold {{ $schedule->enabled ? 'text-amber' : 'text-neutral-500' }}">{{ $schedule->enabled ? 'On' : 'Off' }}</span>
@endif
