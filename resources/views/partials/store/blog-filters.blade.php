@props([
    'categories' => collect(),
    'filters' => null,
    'formId' => 'blogFilters',
    'idPrefix' => 'blog',
    'showActions' => false,
])

<form class="blog-filters" id="{{ $formId }}" method="GET" action="{{ route('blog.index') }}">
    <div class="blog-filters__head">
        <h2 class="blog-filters__title">Explore traditions</h2>
        @if ($filters?->search || $filters?->category || $filters?->featured)
            <a href="{{ route('blog.index') }}" class="blog-filters__clear">Clear all</a>
        @endif
    </div>

    <div class="blog-filters__group">
        <label class="blog-filters__label" for="{{ $idPrefix }}-search">Search Newari insights</label>
        <div class="blog-filters__search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input
                type="search"
                name="q"
                id="{{ $idPrefix }}-search"
                class="blog-filters__search-input"
                value="{{ $filters?->search }}"
                placeholder="Yomari, Indra Jatra, pottery, achar…"
                autocomplete="off"
            >
        </div>
    </div>

    <div class="blog-filters__group">
        <h3 class="blog-filters__label">Category</h3>
        <ul class="blog-filters__list">
            <li>
                <label class="blog-filter-check">
                    <input
                        type="radio"
                        name="category"
                        value=""
                        id="{{ $idPrefix }}-cat-all"
                        @checked(! $filters?->category)
                    >
                    <span class="blog-filter-check__box" aria-hidden="true"></span>
                    <span class="blog-filter-check__text">All topics</span>
                </label>
            </li>
            @foreach ($categories as $category)
                <li>
                    <label class="blog-filter-check">
                        <input
                            type="radio"
                            name="category"
                            value="{{ $category->slug }}"
                            id="{{ $idPrefix }}-cat-{{ $category->slug }}"
                            @checked($filters?->category === $category->slug)
                        >
                        <span class="blog-filter-check__box" aria-hidden="true"></span>
                        <span class="blog-filter-check__text">
                            {{ $category->name }}
                            <span class="blog-filter-check__count">{{ $category->blogs_count }}</span>
                        </span>
                    </label>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="blog-filters__group blog-filters__group--last">
        <h3 class="blog-filters__label">Highlights</h3>
        <ul class="blog-filters__list">
            <li>
                <label class="blog-filter-check">
                    <input
                        type="radio"
                        name="featured"
                        value=""
                        id="{{ $idPrefix }}-featured-all"
                        @checked(! $filters?->featured)
                    >
                    <span class="blog-filter-check__box" aria-hidden="true"></span>
                    <span class="blog-filter-check__text">All posts</span>
                </label>
            </li>
            <li>
                <label class="blog-filter-check">
                    <input
                        type="radio"
                        name="featured"
                        value="featured"
                        id="{{ $idPrefix }}-featured-only"
                        @checked($filters?->featured === 'featured')
                    >
                    <span class="blog-filter-check__box" aria-hidden="true"></span>
                    <span class="blog-filter-check__text">Featured only</span>
                </label>
            </li>
        </ul>
    </div>

    @if ($showActions)
        <div class="blog-filters__actions">
            <button type="submit" class="btn blog-filters__btn-apply">Show results</button>
        </div>
    @else
        <button type="submit" class="visually-hidden">Apply filters</button>
    @endif
</form>
