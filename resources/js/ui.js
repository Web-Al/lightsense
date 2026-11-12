// Small shared UI behaviour: mobile menu and show/hide password.

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
