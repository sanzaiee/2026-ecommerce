@props([
    'brand' => 'Mandira',
    'brandSuffix' => 'Foods',
    'description' => 'Premium dried fruits and traditional pickles crafted with care. 100% natural ingredients, no artificial additives — straight from Nepal to your table.',
    'copyright' => null,
    'quickLinks' => [
        ['label' => 'All Products', 'href' => url('/shop')],
        ['label' => 'Dried Fruits', 'href' => url('/shop') . '?category=dried-fruits'],
        ['label' => 'Pickles', 'href' => url('/shop') . '?category=pickles'],
        ['label' => 'Gift Boxes', 'href' => '#'],
        ['label' => 'Contact Us', 'href' => route('contact')],
    ],
    'informationLinks' => [
        ['label' => 'About Us', 'href' => route('about')],
        ['label' => 'FAQs', 'href' => route('faqs')],
        ['label' => 'Shipping & Delivery', 'href' => '#'],
        ['label' => 'Returns', 'href' => route('refund')],
        ['label' => 'Track Order', 'href' => '#'],
    ],
    'policyLinks' => [
        ['label' => 'Privacy Policy', 'href' => route('privacy')],
        ['label' => 'Terms & Conditions', 'href' => route('terms')],
        ['label' => 'Refund Policy', 'href' => route('refund')],
        ['label' => 'Cookie Policy', 'href' => '#'],
    ],
    'socialLinks' => [
        ['label' => 'Facebook', 'href' => '#', 'icon' => 'bi-facebook'],
        ['label' => 'Instagram', 'href' => '#', 'icon' => 'bi-instagram'],
        ['label' => 'YouTube', 'href' => '#', 'icon' => 'bi-youtube'],
        ['label' => 'TikTok', 'href' => '#', 'icon' => 'bi-tiktok'],
    ],
])

@php
    $site = $site ?? [];
    $brand = $site['siteName'] ?? $brand;
    $brandSuffix = $site['brandSuffix'] ?? $brandSuffix;
    $description = $site['footerDescription'] ?? $description;
    $copyright = $site['copyright'] ?? ($copyright ?? '&copy; ' . date('Y') . ' Mandira Foods. All rights reserved.');
    if (! empty($site['socialLinks'] ?? [])) {
        $socialLinks = $site['socialLinks'];
    }
@endphp

<footer class="site-footer" {{ $attributes }}>
    <div class="footer-newsletter">
        <div class="container">
            <div class="footer-newsletter__inner">
                <div class="footer-newsletter__copy">
                    <h5>Subscribe to our newsletter</h5>
                    <p>Get updates on new products, seasonal offers, and recipes — no spam.</p>
                </div>
                <form class="footer-newsletter__form" method="POST" action="{{ route('newsletter.subscribe') }}">
                    @csrf
                    <label for="footer-newsletter-email" class="visually-hidden">Email address</label>
                    <input type="email" id="footer-newsletter-email" name="email"
                        class="footer-newsletter__input @error('email', 'newsletter') is-invalid @enderror"
                        value="{{ old('email') }}" placeholder="Enter your email" required autocomplete="email">
                    <button type="submit" class="footer-newsletter__btn">Subscribe</button>
                </form>
            </div>
            @if (session('newsletter_status'))
                <p class="footer-newsletter__success" role="status">{{ session('newsletter_status') }}</p>
            @endif
            @error('email', 'newsletter')
                <p class="footer-newsletter__error" role="alert">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="container">
        <div class="row g-4 g-lg-5">
            <div class="col-12 col-md-6 col-lg-3 footer-brand">
                <span class="brand-text">{{ $brand }}<span>{{ $brandSuffix }}</span></span>
                <p>{{ $description }}</p>
            </div>
            <div class="col-6 col-md-3 col-lg-3 footer-col">
                <h6>Quick Links</h6>
                <ul>
                    @foreach ($quickLinks as $link)
                        <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-3 footer-col">
                <h6>Information</h6>
                <ul>
                    @foreach ($informationLinks as $link)
                        <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-12 col-md-6 col-lg-3 footer-col">
                <h6>Policy</h6>
                <ul>
                    @foreach ($policyLinks as $link)
                        <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="social-icons">
                @foreach ($socialLinks as $social)
                    <a href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}">
                        <i class="bi {{ $social['icon'] }}"></i>
                    </a>
                @endforeach
            </div>
            <p class="copyright mb-0">{!! $copyright !!}</p>
        </div>
    </div>
</footer>
