// Live dashboard: polls the server every 2 seconds, sends control changes,
// and draws the "Power use" chart. Does nothing on pages without [data-dashboard].

const root = document.querySelector('[data-dashboard]');

if (root) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const liveUrl = root.dataset.liveUrl;
    const updateUrl = root.dataset.updateUrl;

    const slider = root.querySelector('[data-lux-slider]');
    const dot = root.querySelector('[data-live-dot]');
    const activityList = root.querySelector('[data-activity]');
    const chart = root.querySelector('[data-chart]');
    const chartLabels = root.querySelector('[data-chart-labels]');

    let state = JSON.parse(root.dataset.snapshot);
    let inFlight = 0;
    let dragging = false;

    const ACTIVE_MODE = ['bg-brand', 'font-semibold', 'text-ink'];
    const IDLE_MODE = ['text-white/60', 'hover:text-white'];

    // ---- Drawing -----------------------------------------------------------

    function renderChart(series) {
        const width = 600;
        const height = 160;
        const steps = 30; // 60 seconds at one reading every 2 seconds
        const peak = Math.max(0, ...series);
        const top = Math.max(100, Math.ceil(peak / 20) * 20);

        // Axis labels, highest first
        chartLabels.innerHTML = '';
        for (let i = 5; i >= 0; i--) {
            const item = document.createElement('li');
            item.textContent = `${Math.round((top / 5) * i)} W`;
            chartLabels.appendChild(item);
        }

        // Newest reading sits on the right edge
        const points = series.map((watts, i) => {
            const x = width - (series.length - 1 - i) * (width / steps);
            const y = height - (watts / top) * height;
            return [x, y];
        });

        let grid = '';
        for (let i = 0; i <= 5; i++) {
            const y = (height / 5) * i;
            grid += `<line x1="0" x2="${width}" y1="${y}" y2="${y}" stroke="rgba(255,255,255,0.08)" stroke-width="1" vector-effect="non-scaling-stroke"/>`;
        }

        let plot = '';
        if (points.length > 1) {
            const line = points.map(([x, y]) => `${x.toFixed(1)},${y.toFixed(1)}`).join(' ');
            const first = points[0];
            const last = points[points.length - 1];
            const area = `${first[0].toFixed(1)},${height} ${line} ${last[0].toFixed(1)},${height}`;
            plot = `
                <polygon points="${area}" fill="url(#power-fill)"/>
                <polyline points="${line}" fill="none" stroke="#f5a61b" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"/>`;
        }

        chart.innerHTML = `
            <defs>
                <linearGradient id="power-fill" x1="0" x2="0" y1="0" y2="1">
                    <stop offset="0%" stop-color="#f5a61b" stop-opacity="0.28"/>
                    <stop offset="100%" stop-color="#f5a61b" stop-opacity="0"/>
                </linearGradient>
            </defs>
            ${grid}${plot}`;
    }

    function renderActivity(entries) {
        activityList.innerHTML = '';

        if (!entries.length) {
            const empty = document.createElement('li');
            empty.className = 'py-2.5 text-sm text-white/50';
            empty.textContent = 'Nothing yet.';
            activityList.appendChild(empty);
            return;
        }

        entries.forEach((entry) => {
            const item = document.createElement('li');
            item.className = 'flex items-center justify-between gap-3 py-2.5 text-sm';

            const message = document.createElement('span');
            message.textContent = entry.message;

            const time = document.createElement('span');
            time.className = 'shrink-0 text-xs text-white/40';
            time.textContent = entry.time;

            item.append(message, time);
            activityList.appendChild(item);
        });
    }

    // ---- Applying a snapshot from the server -------------------------------

    function apply(data) {
        state = { ...state, ...data };

        root.querySelectorAll('[data-live]').forEach((el) => {
            const key = el.dataset.live;
            if (key === 'luxThreshold' && dragging) return;
            if (key in data) el.textContent = String(data[key]);
        });

        root.querySelectorAll('[data-live-width]').forEach((el) => {
            const key = el.dataset.liveWidth;
            if (key in data) el.style.width = `${data[key]}%`;
        });

        if (dot) {
            dot.classList.toggle('bg-online', Boolean(data.online));
            dot.classList.toggle('bg-white/30', !data.online);
        }

        if (data.mode) {
            root.querySelectorAll('[data-mode]').forEach((button) => {
                const active = button.dataset.mode === data.mode;
                button.setAttribute('aria-pressed', String(active));
                button.classList.remove(...ACTIVE_MODE, ...IDLE_MODE);
                button.classList.add(...(active ? ACTIVE_MODE : IDLE_MODE));
            });
        }

        if (slider && !dragging && 'luxThreshold' in data) {
            slider.value = data.luxThreshold;
        }

        if (data.schedule) {
            const toggle = root.querySelector(`[data-schedule-id="${data.schedule.id}"]`);
            toggle?.setAttribute('aria-checked', String(data.schedule.enabled));
        }

        if (Array.isArray(data.activity)) renderActivity(data.activity);
        if (Array.isArray(data.series)) renderChart(data.series);
    }

    // ---- Talking to the server ---------------------------------------------

    function request(url, method, body) {
        return fetch(url, {
            method,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body ? JSON.stringify(body) : undefined,
        }).then((response) => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        });
    }

    function showConnectionProblem() {
        const status = root.querySelector('[data-live="statusText"]');
        if (status) status.textContent = 'Connection lost, retrying…';
        dot?.classList.remove('bg-online');
        dot?.classList.add('bg-white/30');
    }

    async function send(url, body) {
        inFlight++;
        try {
            apply(await request(url, 'PATCH', body));
            window.LightSenseToast?.show({ type: 'success', message: 'Your change has been saved.' });
        } catch {
            showConnectionProblem();
            window.LightSenseToast?.show({ type: 'error', message: 'Could not save that change. Check your connection and try again.' });
        } finally {
            inFlight--;
        }
    }

    async function poll() {
        if (document.hidden || inFlight > 0) return;

        inFlight++;
        try {
            apply(await request(liveUrl, 'GET'));
        } catch {
            showConnectionProblem();
        } finally {
            inFlight--;
        }
    }

    // ---- Controls ----------------------------------------------------------

    root.addEventListener('click', (event) => {
        const control = event.target.closest('[data-action]');
        if (!control) return;

        switch (control.dataset.action) {
            case 'toggle-lights':
                send(updateUrl, { lights_on: !state.lightsOn });
                break;
            case 'set-mode':
                send(updateUrl, { mode: control.dataset.mode });
                break;
            case 'toggle-schedule':
                send(control.dataset.url, {});
                break;
        }
    });

    if (slider) {
        const label = root.querySelector('[data-live="luxThreshold"]');

        slider.addEventListener('pointerdown', () => { dragging = true; });
        window.addEventListener('pointerup', () => { dragging = false; });
        slider.addEventListener('input', () => { label.textContent = slider.value; });
        slider.addEventListener('change', () => {
            dragging = false;
            send(updateUrl, { lux_threshold: Number(slider.value) });
        });
    }

    // ---- Start -------------------------------------------------------------

    apply(state);
    setInterval(poll, 2000);
}
