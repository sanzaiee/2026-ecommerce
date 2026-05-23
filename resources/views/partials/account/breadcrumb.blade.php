@props(['section' => null])

<div class="content-page__breadcrumb">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb content-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @if ($section)
                    <li class="breadcrumb-item"><a href="{{ route('account') }}">My Account</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $section }}</li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">My Account</li>
                @endif
            </ol>
        </nav>
    </div>
</div>
