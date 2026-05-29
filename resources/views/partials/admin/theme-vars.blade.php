@php
    $adminColors = $site['adminColors'] ?? [];
    $primary = $adminColors['primary'] ?? config('store.admin.colors.primary', '#3D2914');
    $secondary = $adminColors['secondary'] ?? config('store.admin.colors.secondary', '#C9A227');
    $neutral = $adminColors['neutral'] ?? config('store.admin.colors.neutral', '#7A6B5C');
@endphp
<style>
    :root,
    [data-admin-theme="light"] {
        --admin-color-primary: {{ $primary }};
        --admin-color-secondary: {{ $secondary }};
        --admin-color-neutral: {{ $neutral }};
    }

    [data-admin-theme="dark"] {
        --admin-color-primary: {{ $secondary }};
        --admin-color-secondary: {{ $secondary }};
        --admin-color-neutral: {{ $neutral }};
    }
</style>
