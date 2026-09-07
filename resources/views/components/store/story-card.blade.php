@props([
    'title',
    'excerpt' => '',
    'image' => null,
    'category' => null,
    'date' => null,
    'url' => '#',
])

<article class="story-card">
    <a class="story-card__link" href="{{ $url }}">
        <span class="story-card__media">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $title }}" loading="lazy" decoding="async" width="640" height="400">
            @else
                <span class="story-card__media-fallback" aria-hidden="true">
                    <i class="bi bi-images"></i>
                </span>
            @endif
        </span>
        <span class="story-card__body">
            @if ($category)
                <span class="story-card__category">{{ $category }}</span>
            @endif
            <h3 class="story-card__title">{{ $title }}</h3>
            <p class="story-card__excerpt">{{ $excerpt }}</p>
            <span class="story-card__meta">
                @if ($date)
                    <time class="story-card__date">{{ $date }}</time>
                @endif
                <span class="story-card__more">
                    Read Story
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </span>
            </span>
        </span>
    </a>
</article>