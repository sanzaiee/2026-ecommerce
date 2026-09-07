@php
    $theme = $site['theme'] ?? [];
    $primary = $theme['primary'] ?? config('store.theme.primary', '#b45309');
    $primaryDark = $theme['primaryDark'] ?? config('store.theme.primary_dark', '#92400e');
    $heroAccent = $theme['heroAccent'] ?? config('store.theme.hero_accent', '#f3e7d8');
@endphp
<style>
:root {
    --primary: {{ $primary }};
    --primary-dark: {{ $primaryDark }};
    --hero-teal: {{ $heroAccent }};
    --font-tag: "Cormorant Garamond", Georgia, "Times New Roman", serif;
}
</style>
