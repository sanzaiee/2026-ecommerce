@props([
    'url' => '',
    'alt' => '',
    'size' => 32,
])

@if ($url)
    <img
        src="{{ $url }}"
        alt="{{ $alt }}"
        class="rounded object-fit-cover me-2 align-middle"
        width="{{ $size }}"
        height="{{ $size }}"
    >
@endif
