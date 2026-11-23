// Small shared UI behaviour: mobile menu and show/hide password.

const toastDefaults = {
    success: { title: 'Success', icon: '\u2713' },
    info: { title: 'Notice', icon: 'i' },
    warning: { title: 'Please note', icon: '!' },
    error: { title: 'Something went wrong', icon: '!' },
};

function initializeToasts() {
    const region = document.getElementById('toast-region');
    if (!region) return;

    function dismiss(toast) {
        if (!toast || toast.dataset.toastClosing === 'true') return;

        toast.dataset.toastClosing = 'true';
        toast.classList.add('is-leaving');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
        window.setTimeout(() => toast.remove(), 250);
    }

    function scheduleDismissal(toast) {
        const duration = Number(toast.dataset.toastDuration ?? 6000);
        if (!Number.isFinite(duration) || duration <= 0) return;

        let remaining = duration;
        let startedAt = 0;
        let timer = null;

        const start = () => {
            if (toast.dataset.toastClosing === 'true' || timer !== null) return;
            startedAt = Date.now();
            timer = window.setTimeout(() => dismiss(toast), remaining);
        };

        const pause = () => {
            if (timer === null) return;
            window.clearTimeout(timer);
            timer = null;
            remaining = Math.max(0, remaining - (Date.now() - startedAt));
        };

        toast.addEventListener('mouseenter', pause);
        toast.addEventListener('mouseleave', start);
        toast.addEventListener('focusin', pause);
        toast.addEventListener('focusout', (event) => {
            if (!toast.contains(event.relatedTarget)) start();
        });

        start();
    }

    function createToast({ type = 'info', title, message, duration = type === 'error' ? 0 : 6000 } = {}) {
        const safeType = Object.hasOwn(toastDefaults, type) ? type : 'info';
        const defaults = toastDefaults[safeType];
        const toast = document.createElement('article');
        const icon = document.createElement('span');
        const content = document.createElement('div');
        const heading = document.createElement('p');
        const body = document.createElement('p');
        const close = document.createElement('button');

        toast.className = `toast-card toast-card--${safeType}`;
        toast.dataset.toast = '';
        toast.dataset.toastType = safeType;
        toast.dataset.toastDuration = String(duration);
        toast.setAttribute('role', safeType === 'error' ? 'alert' : 'status');

        icon.className = 'toast-icon';
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = defaults.icon;

        content.className = 'min-w-0 flex-1';
        heading.className = 'toast-title';
        heading.textContent = title || defaults.title;
        body.className = 'toast-message';
        body.textContent = message || '';

        close.type = 'button';
        close.className = 'toast-close';
        close.dataset.toastDismiss = '';
        close.setAttribute('aria-label', 'Dismiss notification');
        close.textContent = '\u00D7';

        content.append(heading, body);
        toast.append(icon, content, close);
        region.prepend(toast);
        scheduleDismissal(toast);

        return toast;
    }

    region.querySelectorAll('[data-toast]').forEach(scheduleDismissal);
    document.querySelectorAll('[data-toast-request]').forEach((request) => {
        createToast({
            type: request.dataset.toastType,
            title: request.dataset.toastTitle,
            message: request.dataset.toastMessage,
            duration: Number(request.dataset.toastDuration ?? 6000),
        });
        request.remove();
    });

    region.addEventListener('click', async (event) => {
        const dismissButton = event.target.closest('[data-toast-dismiss]');
        if (dismissButton) {
            dismiss(dismissButton.closest('[data-toast]'));
            return;
        }

        const copyButton = event.target.closest('[data-toast-copy]');
        if (!copyButton) return;

        try {
            await copyText(copyButton.dataset.toastCopy);
            copyButton.textContent = 'Copied';
            const status = copyButton.parentElement.querySelector('[data-toast-copy-status]');
            if (status) status.textContent = 'Device key copied to clipboard.';
        } catch {
            createToast({ type: 'error', message: 'Could not copy the key. Select and copy it manually.' });
        }
    });

    window.LightSenseToast = {
        show: createToast,
        dismiss: (toast) => dismiss(toast),
    };
}

async function copyText(value) {
    if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(value);
        return;
    }

    const field = document.createElement('textarea');
    field.value = value;
    field.setAttribute('readonly', '');
    field.style.position = 'fixed';
    field.style.opacity = '0';
    document.body.appendChild(field);
    field.select();
    const copied = document.execCommand('copy');
    field.remove();

    if (!copied) throw new Error('Clipboard copy failed.');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeToasts, { once: true });
} else {
    initializeToasts();
}

document.addEventListener('click', (event) => {
    // Mobile navigation (hamburger)
    const menuButton = event.target.closest('[data-menu-toggle]');
    if (menuButton) {
        const menu = document.getElementById(menuButton.dataset.menuToggle);
        if (menu) {
            const open = !menu.classList.toggle('hidden');
            menuButton.setAttribute('aria-expanded', String(open));
            menuButton.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        }
        return;
    }

    // Tapping a link inside the mobile menu closes it
    const menuLink = event.target.closest('#mobile-menu a');
    if (menuLink) {
        document.getElementById('mobile-menu')?.classList.add('hidden');
        document.querySelector('[data-menu-toggle]')?.setAttribute('aria-expanded', 'false');
        return;
    }

    // Show / hide password
    const eye = event.target.closest('[data-password-toggle]');
    if (eye) {
        const input = document.getElementById(eye.dataset.passwordToggle);
        if (!input) return;

        const reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        eye.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
        eye.querySelector('[data-eye-on]')?.classList.toggle('hidden', reveal);
        eye.querySelector('[data-eye-off]')?.classList.toggle('hidden', !reveal);
    }
});
