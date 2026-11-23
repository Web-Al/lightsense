@php
    $dots = ['lights' => 'bg-brass', 'schedule' => 'bg-stone-900/50', 'system' => 'bg-stone-900/25'];
    $filters = ['all' => 'All', 'lights' => 'Lights', 'schedule' => 'Schedules', 'system' => 'System'];
@endphp
<x-layouts.dashboard title="Activity" heading="Activity" :sub="$sub">

    <div class="mt-4 flex flex-wrap gap-2" role="group" aria-label="Filter activity">
        @foreach ($filters as $key => $label)
            <button type="button" data-filter="{{ $key }}"
                class="min-h-10 rounded-sm border px-3 py-2 text-[11px] font-semibold {{ $key === 'all' ? 'border-brass bg-brass text-white' : 'border-line text-stone-600 hover:text-stone-900' }}">{{ $label }}</button>
        @endforeach
    </div>

    <section class="mt-3 rounded-sm border border-line bg-panel-2 p-4">
        @foreach (collect($events)->groupBy(0) as $day => $items)
            <div data-day class="{{ $loop->first ? '' : 'mt-5' }}">
                <h2 class="text-[11px] font-semibold text-stone-600">{{ $day }}</h2>
                <ul class="mt-2 divide-y divide-line">
                    @foreach ($items as [, $type, $text, $time])
                        <li data-type="{{ $type }}" class="flex flex-wrap items-center gap-x-3 gap-y-1 py-2.5 text-xs sm:flex-nowrap">
                            <span class="size-2 shrink-0 rounded-full {{ $dots[$type] }}"></span>
                            <span class="min-w-0 flex-1 break-words">{{ $text }}</span>
                            <span class="metric ml-5 shrink-0 text-[10px] text-stone-500 sm:ml-0">{{ $time }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        <p id="empty" class="hidden text-xs text-stone-500">Nothing to show for this filter.</p>
    </section>

    <x-slot:scripts>
    <script>
        const buttons = document.querySelectorAll('[data-filter]');
        buttons.forEach(btn => btn.addEventListener('click', () => {
            const f = btn.dataset.filter;
            buttons.forEach(b => b.className = 'rounded-sm border px-3 py-1 text-[11px] font-semibold ' +
                (b === btn ? 'border-brass bg-brass text-white' : 'border-line text-stone-600 hover:text-stone-900'));
            let shown = 0;
            document.querySelectorAll('[data-day]').forEach(group => {
                let visible = 0;
                group.querySelectorAll('[data-type]').forEach(li => {
                    const show = f === 'all' || li.dataset.type === f;
                    li.classList.toggle('hidden', !show);
                    if (show) visible++;
                });
                group.classList.toggle('hidden', visible === 0);
                shown += visible;
            });
            document.getElementById('empty').classList.toggle('hidden', shown > 0);
        }));
    </script>
    </x-slot:scripts>
</x-layouts.dashboard>
