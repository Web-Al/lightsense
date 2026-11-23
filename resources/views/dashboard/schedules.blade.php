@php
    $canEdit = auth()->user()->isStaff();
    $color = fn ($s) => $s->enabled ? '#9A6A1F' : '#A8A29E';
    $fcDays = fn (array $days) => array_values(array_unique(array_map(fn ($d) => $d % 7, $days))); // FullCalendar: 0 = Sunday

    // Every schedule becomes a repeating block on the calendar
    $events = [];
    foreach ($schedules as $s) {
        $days = $s->day_numbers;
        $start = substr($s->start_time, 0, 5);
        $end = $s->end_time ? substr($s->end_time, 0, 5) : null;
        $base = ['id' => (string) $s->id, 'title' => $s->name, 'backgroundColor' => $color($s), 'borderColor' => $color($s),
                 'textColor' => '#ffffff', 'classNames' => $s->enabled ? [] : ['sched-off']];

        if ($end === null || $end > $start) {
            $events[] = $base + ['daysOfWeek' => $fcDays($days), 'startTime' => $start, 'endTime' => $end ?? '24:00:00'];
        } else { // crosses midnight: draw the evening part and the morning part on the next day
            $events[] = $base + ['daysOfWeek' => $fcDays($days), 'startTime' => $start, 'endTime' => '24:00:00'];
            $events[] = $base + ['daysOfWeek' => $fcDays(array_map(fn ($d) => $d % 7 + 1, $days)), 'startTime' => '00:00:00', 'endTime' => $end . ':00'];
        }
    }

    // Details used by the edit window
    $details = $schedules->mapWithKeys(fn ($s) => [$s->id => [
        'id' => $s->id, 'name' => $s->name, 'days' => $s->day_numbers,
        'start' => substr($s->start_time, 0, 5), 'end' => $s->end_time ? substr($s->end_time, 0, 5) : '',
        'enabled' => $s->enabled,
    ]]);

    $input = 'w-full rounded-sm border border-line bg-ink/60 px-2.5 py-2 text-sm text-stone-900 focus:border-brass focus:outline-none [color-scheme:light]';
    $lbl = 'mb-1 block text-[11px] font-semibold text-stone-600';
@endphp
<x-layouts.dashboard :calendar="true" title="Schedules" heading="Schedules" sub="Choose when the lights are allowed to turn on">

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <p class="max-w-2xl text-[11px] leading-relaxed text-stone-600">
            @if ($canEdit)
                Click or drag across a time slot to add a schedule. Click a block to change it.
            @else
                You have view-only access. Ask an administrator for Staff access to change schedules.
            @endif
            Lights may turn on only inside an enabled schedule. With no enabled schedule they may turn on at any time.
        </p>
        @if ($canEdit)
            <button type="button" id="add-btn" class="btn btn-brass !py-1.5 !text-xs">Add schedule</button>
        @endif
    </div>

    <section class="mt-3 border border-line bg-panel p-3">
        <div id="calendar" aria-label="Weekly schedule calendar"></div>
    </section>

    {{-- A plain list too: quick on and off switches, and a way to edit without the mouse --}}
    <section class="mt-3 border border-line bg-panel p-4">
        <h2 class="text-[11px] font-semibold">All schedules ({{ $schedules->count() }})</h2>
        @forelse ($schedules as $schedule)
            <div class="flex items-center justify-between gap-3 border-b border-line py-3 last:border-0">
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold {{ $schedule->enabled ? '' : 'text-stone-500' }}">{{ $schedule->name }}</p>
                    <p class="metric text-[10px] text-stone-500">{{ $schedule->summary }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <x-schedule-switch :schedule="$schedule" />
                    <button type="button" data-edit="{{ $schedule->id }}" class="min-h-10 px-2 text-[11px] font-semibold text-brass">{{ $canEdit ? 'Edit' : 'Details' }}</button>
                </div>
            </div>
        @empty
            <p class="mt-3 text-xs text-stone-500">No schedules yet. Lights may turn on at any time.</p>
        @endforelse
    </section>

    {{-- Add / edit window --}}
    <dialog id="sched-dialog" class="m-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-1rem)] max-w-md overflow-y-auto rounded-sm border border-line bg-panel p-0 text-stone-900 backdrop:bg-stone-900/40 sm:w-[calc(100%-2rem)]">
        <div class="p-4 sm:p-5">
            <h2 id="dlg-title" class="text-lg font-bold">New schedule</h2>

            <form id="sched-form" method="POST" action="{{ route('schedules.store') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH" id="f-method" disabled>
                <input type="hidden" name="_edit_id" value="" id="f-edit-id" disabled>

                <div>
                    <label for="f-name" class="{{ $lbl }}">Name</label>
                    <input id="f-name" name="name" maxlength="60" required placeholder="Evening lights" class="{{ $input }}">
                </div>

                <fieldset>
                    <legend class="{{ $lbl }}">Days</legend>
                    <div class="grid grid-cols-7 gap-1">
                        @foreach (\App\Models\Schedule::DAY_NAMES as $number => $label)
                            <label>
                                <input type="checkbox" name="days[]" value="{{ $number }}" class="peer sr-only">
                                <span class="block cursor-pointer border border-line py-1.5 text-center text-[11px] peer-checked:border-brass peer-checked:bg-brass peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-brass">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-1.5 flex gap-3 text-[11px] font-semibold text-brass">
                        <button type="button" data-preset="1,2,3,4,5,6,7">Every day</button>
                        <button type="button" data-preset="1,2,3,4,5">Weekdays</button>
                        <button type="button" data-preset="6,7">Weekends</button>
                    </div>
                </fieldset>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div><label for="f-start" class="{{ $lbl }}">Starts</label>
                        <input id="f-start" name="start_time" type="time" required class="{{ $input }}"></div>
                    <div><label for="f-end" class="{{ $lbl }}">Ends (optional)</label>
                        <input id="f-end" name="end_time" type="time" class="{{ $input }}"></div>
                </div>
                <p class="-mt-2 text-[10px] text-stone-500">Leave "Ends" empty for until midnight. An end time earlier than the start means it ends the next morning.</p>

                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="enabled" value="1" id="f-enabled" class="size-3.5 accent-brass" checked> Schedule is on</label>

                <div class="flex flex-wrap items-center gap-2 pt-1" id="dlg-actions">
                    <button class="btn btn-brass !py-1.5 !text-xs">Save</button>
                    <button type="button" id="dlg-cancel" class="btn btn-ghost !py-1.5 !text-xs">Cancel</button>
                </div>
            </form>

            {{-- Only shown when editing --}}
            <div id="dlg-edit-only" class="mt-4 hidden items-center gap-4 border-t border-line pt-3 text-[11px] font-semibold">
                <form id="toggle-form" method="POST">@csrf @method('PATCH')<button id="toggle-btn" class="text-stone-600 hover:text-stone-900">Turn off</button></form>
                <form id="delete-form" method="POST" onsubmit="return confirm('Delete this schedule?')">@csrf @method('DELETE')<button class="text-stone-500 hover:text-ember">Delete</button></form>
            </div>
        </div>
    </dialog>

    <x-slot:scripts>
    <script>
        const EVENTS = @json($events), DETAILS = @json($details), CAN_EDIT = @json($canEdit);
        const STORE_URL = @json(route('schedules.store')), BASE = @json(url('/dashboard/schedules'));
        const OLD = @json(old()), HAS_ERRORS = @json($errors->any());

        const dlg = document.getElementById('sched-dialog'), form = document.getElementById('sched-form');
        const $ = id => document.getElementById(id);
        const boxes = () => form.querySelectorAll('input[name="days[]"]');
        const pad = n => String(n).padStart(2, '0');
        const hhmm = d => pad(d.getHours()) + ':' + pad(d.getMinutes());
        const isoDay = d => d.getDay() === 0 ? 7 : d.getDay();

        // mode: 'create' or 'edit'. data: name, days[], start, end, enabled
        function openDialog(mode, id, data) {
            const editing = mode === 'edit';
            $('dlg-title').textContent = !CAN_EDIT ? 'Schedule details' : (editing ? 'Edit schedule' : 'New schedule');
            form.action = editing ? BASE + '/' + id : STORE_URL;
            $('f-method').disabled = !editing;
            $('f-edit-id').disabled = !editing;
            $('f-edit-id').value = id || '';

            $('f-name').value = data.name;
            boxes().forEach(cb => cb.checked = data.days.includes(+cb.value));
            $('f-start').value = data.start;
            $('f-end').value = data.end;
            $('f-enabled').checked = data.enabled;

            form.querySelectorAll('input, button[data-preset]').forEach(el => { if (el.type !== 'hidden' && el.name !== '_token') el.disabled = !CAN_EDIT; });
            $('f-method').disabled = !editing; $('f-edit-id').disabled = !editing;
            $('dlg-actions').classList.toggle('hidden', !CAN_EDIT);
            document.querySelectorAll('[data-preset]').forEach(b => b.classList.toggle('hidden', !CAN_EDIT));

            const editOnly = $('dlg-edit-only');
            editOnly.classList.toggle('hidden', !(editing && CAN_EDIT));
            editOnly.classList.toggle('flex', editing && CAN_EDIT);
            if (editing) {
                $('toggle-form').action = BASE + '/' + id + '/toggle';
                $('delete-form').action = BASE + '/' + id;
                $('toggle-btn').textContent = data.enabled ? 'Turn off' : 'Turn on';
            }
            if (!dlg.open) dlg.showModal();
            if (CAN_EDIT) $('f-name').focus();
        }

        const blank = (days, start, end) => ({ name: '', days, start, end, enabled: true });
        const openCreate = (days, start, end) => openDialog('create', null, blank(days, start, end));
        const openEdit = id => DETAILS[id] && openDialog('edit', id, DETAILS[id]);

        $('dlg-cancel').addEventListener('click', () => dlg.close());
        dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); }); // click outside closes
        document.querySelectorAll('[data-preset]').forEach(b => b.addEventListener('click', () => {
            const set = b.dataset.preset.split(',').map(Number);
            boxes().forEach(cb => cb.checked = set.includes(+cb.value));
        }));
        document.querySelectorAll('[data-edit]').forEach(b => b.addEventListener('click', () => openEdit(+b.dataset.edit)));
        if ($('add-btn')) $('add-btn').addEventListener('click', () => openCreate([1, 2, 3, 4, 5, 6, 7], '18:00', '23:00'));

        let calendar;
        calendar = new FullCalendar.Calendar($('calendar'), {
            initialView: window.innerWidth < 768 ? 'listWeek' : 'timeGridWeek',
            headerToolbar: { left: 'today prev,next', center: 'title', right: 'timeGridWeek,dayGridMonth,listWeek' },
            buttonText: { today: 'Today', week: 'Week', month: 'Month', list: 'List' },
            allDaySlot: false,
            nowIndicator: true,
            scrollTime: '06:00:00',
            height: window.innerWidth < 768 ? 'auto' : 640,
            windowResize: () => {
                const compact = window.innerWidth < 768;
                if (compact && calendar.view.type === 'timeGridWeek') calendar.changeView('listWeek');
                if (!compact && calendar.view.type === 'listWeek') calendar.changeView('timeGridWeek');
                calendar.setOption('height', compact ? 'auto' : 640);
            },
            slotLabelFormat: { hour: 'numeric', meridiem: 'short' },
            eventTimeFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
            selectable: CAN_EDIT,
            selectMirror: true,
            events: EVENTS,
            eventClick: info => openEdit(+info.event.id),
            select: info => {
                if (info.allDay) { // month view: whole days were chosen
                    const days = [];
                    for (let t = new Date(info.start); t < info.end; t.setDate(t.getDate() + 1)) days.push(isoDay(t));
                    openCreate(days, '18:00', '23:00');
                } else {
                    const endsAtMidnight = info.end.getHours() === 0 && info.end.getMinutes() === 0;
                    openCreate([isoDay(info.start)], hhmm(info.start), endsAtMidnight ? '' : hhmm(info.end));
                }
                calendar.unselect();
            },
        });
        calendar.render();

        // After a failed save, open the window again with what was typed
        if (HAS_ERRORS && OLD && OLD.name !== undefined) {
            const data = { name: OLD.name || '', days: (OLD.days || []).map(Number), start: OLD.start_time || '', end: OLD.end_time || '', enabled: !!OLD.enabled };
            if (OLD._edit_id && DETAILS[OLD._edit_id]) openDialog('edit', OLD._edit_id, data); else openDialog('create', null, data);
        }
    </script>
    </x-slot:scripts>
</x-layouts.dashboard>
