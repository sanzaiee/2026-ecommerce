<div class="col-md-6 col-lg-4">
    <article class="testimonial-card">
        <div class="testimonial-card__stars" aria-label="{{ $testimonial['rating'] }} out of 5 stars">
            @for ($i = 1; $i <= 5; $i++)
                <i class="bi bi-star{{ $i <= $testimonial['rating'] ? '-fill' : '' }}" aria-hidden="true"></i>
            @endfor
        </div>
        <blockquote class="testimonial-card__quote">
            <p>&ldquo;{{ $testimonial['text'] }}&rdquo;</p>
        </blockquote>
        <footer class="testimonial-card__footer">
            <cite class="testimonial-card__name">{{ $testimonial['name'] }}</cite>
            @if (! empty($testimonial['productName']) && ! empty($testimonial['productUrl']))
                <span class="testimonial-card__product">
                    on <a href="{{ $testimonial['productUrl'] }}">{{ $testimonial['productName'] }}</a>
                </span>
            @endif
        </footer>
    </article>
</div>
