@extends('layouts.admin')

@section('title', 'Landing Page')

@section('content')
<h1 class="h3 mb-4">Landing Page CMS</h1>
<div class="admin-card">
    <form method="POST" action="{{ route('admin.landing-page.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Hero title</label>
                <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $page->hero_title) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero subtitle</label>
                <input type="text" name="hero_subtitle" class="form-control" value="{{ old('hero_subtitle', $page->hero_subtitle) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Hero image</label>
                <input type="file" name="hero_image" class="form-control" accept="image/*">
                @if ($page->getFirstMediaUrl('cms'))
                    <img src="{{ $page->getFirstMediaUrl('cms', 'large') }}" alt="" class="mt-2 rounded" width="200">
                @endif
            </div>
            <div class="col-12">
                <label class="form-label">Featured categories</label>
                <select name="featured_category_ids[]" class="form-select" multiple size="4">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(in_array($cat->id, old('featured_category_ids', $selectedCategoryIds)))>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Everyday products</label>
                <select name="everyday_product_ids[]" class="form-select" multiple size="8">
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" @selected(in_array($p->id, old('everyday_product_ids', $everydayIds)))>{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Top selling products</label>
                <select name="top_selling_product_ids[]" class="form-select" multiple size="8">
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" @selected(in_array($p->id, old('top_selling_product_ids', $topSellingIds)))>{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Meta title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
            </div>
            <div class="col-md-8">
                <label class="form-label">Meta description</label>
                <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $page->meta_description) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Meta keywords</label>
                <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $page->meta_keywords) }}" placeholder="Thimi pottery, Nepal clay pots, Newar pottery">
                <small class="text-muted">Comma-separated keywords used in the page meta tags.</small>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-dark">Save landing page</button>
        </div>
    </form>
</div>
@endsection
