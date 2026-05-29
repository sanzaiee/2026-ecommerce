@php
    $adminThemeDefault = $site['adminTheme'] ?? config('store.admin.theme', 'light');
@endphp
<script>
    (function () {
        var configured = @json($adminThemeDefault);
        try {
            var stored = localStorage.getItem('admin-cms-theme');
            var pref = (stored === 'light' || stored === 'dark' || stored === 'system') ? stored : configured;
            var dark = pref === 'dark' || (pref === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.setAttribute('data-admin-theme', dark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-admin-theme-pref', pref);
            document.documentElement.setAttribute('data-admin-theme-default', configured);
        } catch (e) {
            document.documentElement.setAttribute('data-admin-theme-default', configured);
        }
    })();
</script>
