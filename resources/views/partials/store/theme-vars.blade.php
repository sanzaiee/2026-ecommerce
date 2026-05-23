@php
    $theme = $site['theme'] ?? [];
    $primary = $theme['primary'] ?? config('store.theme.primary', '#b91c1c');
    $primaryDark = $theme['primaryDark'] ?? config('store.theme.primary_dark', '#991b1b');
    $heroAccent = $theme['heroAccent'] ?? config('store.theme.hero_accent', '#dceee9');
@endphp
<style>
:root {
    --primary: {{ $primary }};
    --primary-dark: {{ $primaryDark }};
    --hero-teal: {{ $heroAccent }};
}
</style>
