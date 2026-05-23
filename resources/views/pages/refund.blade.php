@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', $pageTitle . ' — ' . ($site['siteName'] ?? 'Mandira') . ' ' . ($site['brandSuffix'] ?? 'Foods'))

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page">
        @include('partials.store.content-breadcrumb', ['title' => $pageTitle])

        <section class="static-page">
            <div class="container">
                <header class="static-page__header">
                    <h1>Refund Policy</h1>
                    @if ($lastUpdated)
                        <p class="static-page__meta">Last updated: {{ $lastUpdated }}</p>
                    @endif
                </header>

                @include('partials.store.policy-body', [
                    'legalBody' => $legalBody,
                    'fallback' => view('partials.store.policy-fallback-refund')->render(),
                ])
            </div>
        </section>
    </div>
@endsection
