@props([
    'idPrefix' => 'shop',
    'formId' => 'shopFilters',
    'showActions' => false,
])

<form class="shop-filters" id="{{ $formId }}" data-shop-filters novalidate>
    <div class="shop-filters__head">
        <h2 class="shop-filters__title">Filters</h2>
        <button type="button" class="shop-filters__clear" data-action="clear-filters" hidden>
            Clear all
        </button>
    </div>

    <div class="shop-filters__group">
        <h3 class="shop-filters__label">Category</h3>
        <ul class="shop-filters__list">
            <li>
                <label class="shop-filter-check">
                    <input type="checkbox" name="category" value="dried-fruits"
                        id="{{ $idPrefix }}-cat-dried">
                    <span class="shop-filter-check__box" aria-hidden="true"></span>
                    <span class="shop-filter-check__text">Dried Fruits</span>
                </label>
            </li>
            <li>
                <label class="shop-filter-check">
                    <input type="checkbox" name="category" value="pickles" id="{{ $idPrefix }}-cat-pickles">
                    <span class="shop-filter-check__box" aria-hidden="true"></span>
                    <span class="shop-filter-check__text">Pickles</span>
                </label>
            </li>
        </ul>
    </div>

    <div class="shop-filters__group">
        <h3 class="shop-filters__label">Price</h3>
        <div class="shop-filters__price">
            <div class="shop-filters__price-field">
                <label for="{{ $idPrefix }}-price-min" class="visually-hidden">Minimum price</label>
                <span class="shop-filters__price-prefix">Rs.</span>
                <input type="number" id="{{ $idPrefix }}-price-min" name="price_min" min="0" step="10"
                    placeholder="Min" inputmode="numeric" data-filter-price-min>
            </div>
            <span class="shop-filters__price-sep" aria-hidden="true">—</span>
            <div class="shop-filters__price-field">
                <label for="{{ $idPrefix }}-price-max" class="visually-hidden">Maximum price</label>
                <span class="shop-filters__price-prefix">Rs.</span>
                <input type="number" id="{{ $idPrefix }}-price-max" name="price_max" min="0" step="10"
                    placeholder="Max" inputmode="numeric" data-filter-price-max>
            </div>
        </div>
    </div>

    <div class="shop-filters__group">
        <h3 class="shop-filters__label">Availability</h3>
        <ul class="shop-filters__list">
            <li>
                <label class="shop-filter-check">
                    <input type="checkbox" name="availability" value="in-stock"
                        id="{{ $idPrefix }}-avail-in">
                    <span class="shop-filter-check__box" aria-hidden="true"></span>
                    <span class="shop-filter-check__text">In stock</span>
                </label>
            </li>
            <li>
                <label class="shop-filter-check">
                    <input type="checkbox" name="availability" value="out-of-stock"
                        id="{{ $idPrefix }}-avail-out">
                    <span class="shop-filter-check__box" aria-hidden="true"></span>
                    <span class="shop-filter-check__text">Out of stock</span>
                </label>
            </li>
        </ul>
    </div>

    <div class="shop-filters__group shop-filters__group--last">
        <h3 class="shop-filters__label">Rating</h3>
        <ul class="shop-filters__list">
            <li>
                <label class="shop-filter-radio">
                    <input type="radio" name="rating" value="4" id="{{ $idPrefix }}-rating-4">
                    <span class="shop-filter-radio__dot" aria-hidden="true"></span>
                    <span class="shop-filter-radio__text">
                        <span class="shop-filter-stars" aria-hidden="true">
                            @for ($i = 0; $i < 4; $i++)
                                <i class="bi bi-star-fill"></i>
                            @endfor
                            <i class="bi bi-star"></i>
                        </span>
                        4 &amp; above
                    </span>
                </label>
            </li>
            <li>
                <label class="shop-filter-radio">
                    <input type="radio" name="rating" value="3" id="{{ $idPrefix }}-rating-3">
                    <span class="shop-filter-radio__dot" aria-hidden="true"></span>
                    <span class="shop-filter-radio__text">
                        <span class="shop-filter-stars" aria-hidden="true">
                            @for ($i = 0; $i < 3; $i++)
                                <i class="bi bi-star-fill"></i>
                            @endfor
                            @for ($i = 0; $i < 2; $i++)
                                <i class="bi bi-star"></i>
                            @endfor
                        </span>
                        3 &amp; above
                    </span>
                </label>
            </li>
            <li>
                <label class="shop-filter-radio">
                    <input type="radio" name="rating" value="" id="{{ $idPrefix }}-rating-any" checked>
                    <span class="shop-filter-radio__dot" aria-hidden="true"></span>
                    <span class="shop-filter-radio__text">Any rating</span>
                </label>
            </li>
        </ul>
    </div>

    @if ($showActions)
        <div class="shop-filters__actions">
            <button type="button" class="btn shop-filters__btn-apply" data-action="apply-filters">
                Show results
            </button>
        </div>
    @endif
</form>
