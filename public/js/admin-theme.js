(function () {
    const STORAGE_KEY = 'admin-cms-theme';
    const root = document.documentElement;

    function getConfiguredDefault() {
        const value = root.getAttribute('data-admin-theme-default');

        return value === 'light' || value === 'dark' || value === 'system' ? value : 'light';
    }

    function getStored() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);

            if (stored === 'light' || stored === 'dark' || stored === 'system') {
                return stored;
            }
        } catch {
            /* ignore */
        }

        return getConfiguredDefault();
    }

    function setStored(value) {
        try {
            localStorage.setItem(STORAGE_KEY, value);
        } catch {
            /* ignore */
        }
    }

    function clearStored() {
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch {
            /* ignore */
        }
    }

    function systemPrefersDark() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function resolvedTheme(preference) {
        if (preference === 'system') {
            return systemPrefersDark() ? 'dark' : 'light';
        }

        return preference === 'dark' ? 'dark' : 'light';
    }

    function apply(preference) {
        const resolved = resolvedTheme(preference);
        root.setAttribute('data-admin-theme', resolved);
        root.setAttribute('data-admin-theme-pref', preference);

        document.querySelectorAll('[data-admin-theme]').forEach((btn) => {
            const isActive = btn.getAttribute('data-admin-theme') === preference;
            btn.classList.toggle('is-active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    apply(getStored());

    document.querySelectorAll('[data-admin-theme]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const preference = btn.getAttribute('data-admin-theme');
            if (!preference) {
                return;
            }
            setStored(preference);
            apply(preference);
        });
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (getStored() === 'system') {
            apply('system');
        }
    });

    window.AdminTheme = {
        getPreference: getStored,
        getConfiguredDefault,
        apply,
        resetToConfigured: () => {
            clearStored();
            apply(getConfiguredDefault());
        },
    };
})();
