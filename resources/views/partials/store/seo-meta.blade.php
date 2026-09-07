@php
    /** @var \App\Support\ViewData\DTOs\Seo $seo */
    $seoUrl = rtrim(config('app.url'), '/');
    $organizationLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $site['siteName'] ?? 'Mandira',
        'url' => $seoUrl,
        'logo' => $site['faviconUrl'],
    ];
    $websiteLd = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $site['siteName'] ?? 'Mandira',
        'url' => $seoUrl,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => $seoUrl.'/shop?search={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];
@endphp

<link rel="canonical" href="{{ $seo->canonicalUrl() }}">

<meta name="description" content="{{ $seo->description }}">

@if ($ogImage = $seo->ogImageUrl())
    <meta property="og:image" content="{{ $ogImage }}">
@endif
@if (!empty($site['siteName']))
    <meta property="og:site_name" content="{{ $site['siteName'] }}">
@endif
<meta property="og:type" content="{{ $seo->ogType }}">
<meta property="og:title" content="{{ $seo->title }}">
<meta property="og:description" content="{{ $seo->description }}">
<meta property="og:url" content="{{ $seo->canonicalUrl() }}">
<meta property="og:locale" content="en_NP">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->title }}">
<meta name="twitter:description" content="{{ $seo->description }}">

@if ($seo->keywords)
    <meta name="keywords" content="{{ implode(', ', $seo->keywords) }}">
@endif

<script type="application/ld+json">{!! json_encode($organizationLd) !!}</script>
<script type="application/ld+json">{!! json_encode($websiteLd) !!}</script>