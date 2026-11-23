@php
    $toasts = [];
    $flashTypes = [
        'success' => ['success', 'Success'],
        'status' => ['success', 'Success'], // Backward compatibility for existing flash messages.
        'info' => ['info', 'Notice'],
        'warning' => ['warning', 'Please note'],
        'error' => ['error', 'Something went wrong'],
    ];

    foreach ($flashTypes as $key => [$type, $title]) {
        $message = session($key);

        if (filled($message) && is_string($message)) {
            $toasts[] = compact('type', 'title', 'message');
        }
    }

    if ($errors->any()) {
        $toasts[] = [
            'type' => 'error',
            'title' => 'Please check the form',
            'messages' => $errors->all(),
        ];
    }

    $newKey = session('new_key');
@endphp

<div id="toast-region" aria-label="Notifications" aria-live="polite" aria-relevant="additions text">
    @foreach ($toasts as $toast)
        <article class="toast-card toast-card--{{ $toast['type'] }}" data-toast data-toast-type="{{ $toast['type'] }}" data-toast-duration="{{ $toast['type'] === 'error' ? 0 : 6000 }}" role="{{ $toast['type'] === 'error' ? 'alert' : 'status' }}">
            <span class="toast-icon" aria-hidden="true">
                @if ($toast['type'] === 'success')
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m4 10 4 4 8-8" /></svg>
                @else
                    {{ $toast['type'] === 'error' ? '!' : 'i' }}
                @endif
            </span>
            <div class="min-w-0 flex-1">
                <p class="toast-title">{{ $toast['title'] }}</p>
                @if (isset($toast['messages']))
                    <ul class="toast-message-list">
                        @foreach ($toast['messages'] as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="toast-message">{{ $toast['message'] }}</p>
                @endif
            </div>
            <button type="button" class="toast-close" data-toast-dismiss aria-label="Dismiss notification">&times;</button>
        </article>
    @endforeach

    @if (is_array($newKey) && filled($newKey['key'] ?? null))
        <article class="toast-card toast-card--info" data-toast data-toast-type="info" data-toast-duration="0" role="status">
            <span class="toast-icon" aria-hidden="true">i</span>
            <div class="min-w-0 flex-1">
                <p class="toast-title">Device key for {{ $newKey['device'] ?? 'device' }}</p>
                <p class="toast-message">Copy this key now. It will not be shown again.</p>
                <code class="toast-copy-value">{{ $newKey['key'] }}</code>
                <p class="toast-message mt-2">Send readings to <span class="font-semibold">{{ url('/api/readings') }}</span> with <span class="font-semibold">Authorization: Bearer (the key)</span>.</p>
                <button type="button" class="toast-copy-button" data-toast-copy="{{ $newKey['key'] }}">Copy key</button>
                <span class="sr-only" data-toast-copy-status aria-live="polite"></span>
            </div>
            <button type="button" class="toast-close" data-toast-dismiss aria-label="Dismiss notification">&times;</button>
        </article>
    @endif
</div>
