@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $pageTitle . ' — ' . ($site['siteName'] ?? 'Mandira') . ' ' . ($site['brandSuffix'] ?? 'Foods'))

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page">
        @include('partials.store.content-breadcrumb', [
            'title' => $post->title,
            'parent' => ['label' => 'Blog', 'href' => route('blog.index')],
        ])

        <section class="static-page">
            <div class="container">
                <header class="static-page__header">
                    @if ($post->category)
                        <a class="blog-show__category" href="{{ route('blog.index', ['category' => $post->category->slug]) }}">
                            {{ $post->category->name }}
                        </a>
                    @endif
                    <h1>{{ $post->title }}</h1>
                    <p class="static-page__meta">
                        @if ($post->is_featured)
                            Featured ·
                        @endif
                        <time datetime="{{ $post->created_at?->toDateString() }}">{{ $post->created_at?->format('F j, Y') }}</time>
                    </p>
                </header>

                @if ($post->hasImage())
                    <figure class="blog-show__figure">
                        <img
                            class="blog-show__image"
                            src="{{ $post->imageUrl('medium') }}"
                            alt="{{ $post->title }}"
                            width="1200"
                            height="800"
                            loading="eager"
                            decoding="async"
                        >
                    </figure>
                @endif

                <article class="static-page__body">
                    {!! $post->content !!}
                </article>
            </div>
        </section>
    </div>
@endsection
