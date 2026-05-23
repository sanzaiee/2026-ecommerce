@extends('layouts.admin')

@section('title', 'About Page')

@section('content')
@php
    $values = old('values', $page->values ?? []);
    $processSteps = old('process_steps', $page->process_steps ?? []);
@endphp

<h1 class="h3 mb-4">About Page CMS</h1>
<div class="admin-card">
    <form method="POST" action="{{ route('admin.about-page.update') }}" enctype="multipart/form-data" data-rich-text>
        @csrf @method('PUT')

        <h2 class="h5 mb-3">Hero</h2>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Eyebrow</label>
                <input type="text" name="hero_eyebrow" class="form-control" value="{{ old('hero_eyebrow', $page->hero_eyebrow) }}">
            </div>
            <div class="col-md-8">
                <label class="form-label">Lead (leave blank to use site tagline)</label>
                <input type="text" name="hero_lead" class="form-control" value="{{ old('hero_lead', $page->hero_lead) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero image</label>
                <input type="file" name="hero_image" class="form-control" accept="image/*">
                @if ($page->getFirstMediaUrl('hero'))
                    <img src="{{ $page->getFirstMediaUrl('hero', 'large') }}" alt="" class="mt-2 rounded" width="200">
                @endif
            </div>
        </div>

        <h2 class="h5 mb-3">Value cards</h2>
        <div class="row g-3 mb-4">
            @for ($i = 0; $i < 4; $i++)
                @php $value = $values[$i] ?? ['icon' => '', 'title' => '', 'text' => '']; @endphp
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <p class="small text-muted mb-2">Card {{ $i + 1 }}</p>
                        <label class="form-label">Icon class</label>
                        <input type="text" name="values[{{ $i }}][icon]" class="form-control mb-2"
                            value="{{ old("values.{$i}.icon", $value['icon'] ?? '') }}" placeholder="bi-flower1">
                        <label class="form-label">Title</label>
                        <input type="text" name="values[{{ $i }}][title]" class="form-control mb-2"
                            value="{{ old("values.{$i}.title", $value['title'] ?? '') }}">
                        <label class="form-label">Text</label>
                        <textarea name="values[{{ $i }}][text]" class="form-control" rows="2">{{ old("values.{$i}.text", $value['text'] ?? '') }}</textarea>
                    </div>
                </div>
            @endfor
        </div>

        <h2 class="h5 mb-3">Story</h2>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="form-label">Heading</label>
                <input type="text" name="story_heading" class="form-control" value="{{ old('story_heading', $page->story_heading) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Paragraph 1</label>
                <textarea name="story_paragraph_1" class="form-control" rows="4">{{ old('story_paragraph_1', $page->story_paragraph_1) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Paragraph 2</label>
                <textarea name="story_paragraph_2" class="form-control" rows="4">{{ old('story_paragraph_2', $page->story_paragraph_2) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Story image</label>
                <input type="file" name="story_image" class="form-control" accept="image/*">
                @if ($page->getFirstMediaUrl('story'))
                    <img src="{{ $page->getFirstMediaUrl('story', 'large') }}" alt="" class="mt-2 rounded" width="200">
                @endif
            </div>
        </div>

        <h2 class="h5 mb-3">Gallery</h2>
        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <label class="form-label">Section heading</label>
                <input type="text" name="gallery_heading" class="form-control" value="{{ old('gallery_heading', $page->gallery_heading) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Section lead</label>
                <input type="text" name="gallery_lead" class="form-control" value="{{ old('gallery_lead', $page->gallery_lead) }}">
            </div>
        </div>
        <div class="row g-3 mb-4">
            @for ($i = 0; $i < 3; $i++)
                @php $media = $galleryMedia[$i] ?? null; @endphp
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <p class="small text-muted mb-2">Image {{ $i + 1 }}</p>
                        <input type="file" name="gallery_images[{{ $i }}]" class="form-control mb-2" accept="image/*">
                        @if ($media)
                            <img src="{{ $page->mediaUrl($media) }}" alt="" class="img-fluid rounded mb-2">
                        @endif
                        <label class="form-label">Alt text</label>
                        <input type="text" name="gallery_alts[{{ $i }}]" class="form-control mb-2"
                            value="{{ old("gallery_alts.{$i}", $media?->getCustomProperty('alt')) }}">
                        <label class="form-label">Caption</label>
                        <input type="text" name="gallery_captions[{{ $i }}]" class="form-control"
                            value="{{ old("gallery_captions.{$i}", $media?->getCustomProperty('caption')) }}">
                    </div>
                </div>
            @endfor
        </div>

        <h2 class="h5 mb-3">Process</h2>
        <div class="row g-3 mb-2">
            <div class="col-12">
                <label class="form-label">Section heading</label>
                <input type="text" name="process_heading" class="form-control" value="{{ old('process_heading', $page->process_heading) }}">
            </div>
        </div>
        <div class="row g-3 mb-4">
            @for ($i = 0; $i < 3; $i++)
                @php $step = $processSteps[$i] ?? ['title' => '', 'text' => '']; @endphp
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <p class="small text-muted mb-2">Step {{ $i + 1 }}</p>
                        <label class="form-label">Title</label>
                        <input type="text" name="process_steps[{{ $i }}][title]" class="form-control mb-2"
                            value="{{ old("process_steps.{$i}.title", $step['title'] ?? '') }}">
                        <label class="form-label">Description</label>
                        <textarea name="process_steps[{{ $i }}][text]" class="form-control" rows="2">{{ old("process_steps.{$i}.text", $step['text'] ?? '') }}</textarea>
                    </div>
                </div>
            @endfor
            @for ($i = 0; $i < 2; $i++)
                @php $media = $craftMedia[$i] ?? null; @endphp
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <p class="small text-muted mb-2">Craft image {{ $i + 1 }}</p>
                        <input type="file" name="craft_images[{{ $i }}]" class="form-control mb-2" accept="image/*">
                        @if ($media)
                            <img src="{{ $page->mediaUrl($media) }}" alt="" class="img-fluid rounded mb-2">
                        @endif
                        <label class="form-label">Alt text</label>
                        <input type="text" name="craft_alts[{{ $i }}]" class="form-control"
                            value="{{ old("craft_alts.{$i}", $media?->getCustomProperty('alt')) }}">
                    </div>
                </div>
            @endfor
        </div>

        <h2 class="h5 mb-3">Editorial &amp; CTA</h2>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="form-label d-flex justify-content-between">
                    <span>More about us (HTML)</span>
                    <span class="text-muted small">Last updated: {{ $page->updated_at?->format('M j, Y') }}</span>
                </label>
                <div class="form-text mb-2">Shown at <a href="{{ route('about') }}" target="_blank" rel="noopener">/about</a>.</div>
                <textarea name="editorial_body" class="form-control rich-text" rows="10">{{ old('editorial_body', $page->editorial_body) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">CTA title</label>
                <input type="text" name="cta_title" class="form-control" value="{{ old('cta_title', $page->cta_title) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">CTA text</label>
                <input type="text" name="cta_text" class="form-control" value="{{ old('cta_text', $page->cta_text) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Meta description</label>
                <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $page->meta_description) }}">
            </div>
        </div>

        <button type="submit" class="btn btn-dark">Save about page</button>
    </form>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
    <script src="{{ asset('js/admin-rich-text.js') }}"></script>
@endpush
