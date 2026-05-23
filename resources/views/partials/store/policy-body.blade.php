<article class="static-page__body">
    @if (!empty($legalBody))
        {!! $legalBody !!}
    @else
        {!! $fallback ?? '' !!}
    @endif
</article>
