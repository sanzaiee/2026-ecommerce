@props(['title', 'parent' => null])

<div class="content-page__breadcrumb">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb content-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @if ($parent)
                    <li class="breadcrumb-item"><a href="{{ $parent['href'] }}">{{ $parent['label'] }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
            </ol>
        </nav>
    </div>
</div>
