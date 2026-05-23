@props([
    'url' => '',
    'alt' => '',
    'removeUrl' => null,
    'confirm' => 'Remove this image?',
    'width' => 120,
    'height' => 120,
])

@if ($url)
    <div {{ $attributes->class(['admin-media-preview', 'd-inline-block', 'mt-2']) }}>
        <img
            src="{{ $url }}"
            alt="{{ $alt }}"
            class="admin-media-preview__img rounded"
            width="{{ $width }}"
            height="{{ $height }}"
        >
        @if ($removeUrl)
            <button
                type="button"
                class="btn btn-danger btn-sm rounded-circle admin-media-preview__remove-btn"
                title="Remove image"
                aria-label="Remove image"
                data-remove-url="{{ $removeUrl }}"
                data-confirm="{{ $confirm }}"
                data-csrf="{{ csrf_token() }}"
            >
                <i class="bi bi-x-lg"></i>
            </button>
        @endif
    </div>
@endif
