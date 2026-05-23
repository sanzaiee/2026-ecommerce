@extends('layouts.account')

@section('account-content')
    <div class="account-header">
        <h1 class="account-header__title">My wishlist</h1>
        <p class="account-header__lead">Products you have saved for later. Move them to cart when you are ready to order.</p>
    </div>

    <div class="account-card account-wishlist-page @if ($wishlistItems->total() === 0) is-empty @endif"
        data-wishlist-page data-wishlist-paginated>
        <div class="account-wishlist-toolbar">
            <p class="account-wishlist-count" id="wishlistPageCount">
                @if ($wishlistItems->total() === 1)
                    1 item saved
                @else
                    {{ $wishlistItems->total() }} items saved
                @endif
                @if ($wishlistItems->hasPages())
                    <span class="account-wishlist-count__page">
                        &middot; Showing {{ $wishlistItems->firstItem() }}&ndash;{{ $wishlistItems->lastItem() }}
                    </span>
                @endif
            </p>
            <button type="button" class="account-btn account-btn--primary account-btn--inline" id="wishlistPageMoveAll"
                @if ($wishlistItems->total() === 0) disabled @endif>
                <i class="bi bi-bag-plus" aria-hidden="true"></i>
                Move all to cart
            </button>
        </div>

        @include('partials.account.wishlist-items', ['items' => $wishlistItems])

        @if ($wishlistItems->hasPages())
            <div class="account-pagination">
                {{ $wishlistItems->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('wishlistPageMoveAll')?.addEventListener('click', () => {
            document.getElementById('wishlistMoveAllBtn')?.click();
        });
    </script>
@endpush
