@php
    $searchValue = $value ?? request('search', '');
    $searchClass = $class ?? '';
@endphp

<form class="search-bar {{ $searchClass }}" action="{{ route('shop') }}" method="get" role="search"
    data-store-search>
    <input type="search" name="search" value="{{ $searchValue }}" placeholder="Search products..."
        aria-label="Search products" autocomplete="off" maxlength="100" data-store-search-input>
    <button type="submit" class="search-bar__submit" aria-label="Submit search">
        <i class="bi bi-search search-icon" aria-hidden="true"></i>
    </button>
</form>
