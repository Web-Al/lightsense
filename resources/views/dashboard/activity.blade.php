@php
    $dots = ['lights' => 'bg-amber', 'schedule' => 'bg-sky-400', 'system' => 'bg-green-500'];
    $filters = ['all' => 'All', 'lights' => 'Lights', 'schedule' => 'Schedules', 'system' => 'System'];
@endphp
<x-layouts.dashboard title="Activity" heading="Activity" sub="Everything that happened in the Living room (sample data)">

    <div class="mt-4 flex flex-wrap gap-2" role="group" aria-label="Filter activity">
        @foreach ($filters as $key => $label)
            <button type="button" data-filter="{{ $key }}"
                class="rounded-full border px-3 py-1 text-[11px] font-semibold {{ $key === 'all' ? 'border-amber bg-amber text-black' : 'border-line text-neutral-400 hover:text-white' }}">{{ $label }}</button>
        @endforeach
    </div>

    <section class="mt-3 rounded-lg border border-line bg-panel-2 p-4">
        @foreach (collect($events)->groupBy(0) as $day => $items)
            <div data-day class="{{ $loop->first ? '' : 'mt-5' }}">
                <h2 class="text-[11px] font-semibold text-neutral-400">{{ $day }}</h2>
                <ul class="mt-2 divide-y divide-line">
                    @foreach ($items as [, $type, $text, $time])
                        <li data-type="{{ $type }}" class="flex items-center gap-3 py-2.5 text-xs">
                            <span class="size-2 shrink-0 rounded-full {{ $dots[$type] }}"></span>
                            <span class="flex-1">{{ $text }}</span>
                            <span class="shrink-0 text-[10px] text-neutral-500">{{ $time }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        <p id="empty" class="hidden text-xs text-neutral-500">Nothing to show for this filter.</p>
    </section>

    <x-slot:scripts>
    <script>
        const buttons = document.querySelectorAll('[data-filter]');
        buttons.forEach(btn => btn.addEventListener('click', () => {
            const f = btn.dataset.filter;
            buttons.forEach(b => b.className = 'rounded-full border px-3 py-1 text-[11px] font-semibold ' +
                (b === btn ? 'border-amber bg-amber text-black' : 'border-line text-neutral-400 hover:text-white'));
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
