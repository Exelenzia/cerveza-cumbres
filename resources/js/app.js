function storedTheme() {
    var stored = localStorage.getItem('theme');
    return stored || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
}

function currentTheme() {
    return document.documentElement.classList.contains('light') ? 'light' : 'dark';
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('light', theme === 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.setAttribute('aria-label', theme === 'light' ? 'Cambiar a modo oscuro' : 'Cambiar a modo claro');

        const icon = btn.querySelector('[data-theme-icon]');
        if (icon) icon.textContent = theme === 'light' ? '☀' : '☾';

        const label = btn.querySelector('[data-theme-label]');
        if (label) label.textContent = theme === 'light' ? 'Claro' : 'Oscuro';
    });
}

document.addEventListener('click', (event) => {
    const btn = event.target.closest('[data-theme-toggle]');
    if (!btn) return;

    const next = currentTheme() === 'light' ? 'dark' : 'light';
    localStorage.setItem('theme', next);
    applyTheme(next);
});

document.addEventListener('livewire:navigated', () => applyTheme(storedTheme()));

applyTheme(storedTheme());
