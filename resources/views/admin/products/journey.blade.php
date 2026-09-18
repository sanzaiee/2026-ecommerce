@extends('layouts.admin')

@section('title', 'Product Journey')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
        <div>
            <p class="text-muted small mb-1">
                <a href="{{ route('admin.products.index') }}" class="text-decoration-none">Products</a>
                <span class="mx-1">/</span>
                Journey
            </p>
            <h1 class="h3 mb-1">{{ $product->title }}</h1>
            <p class="text-muted mb-0">Set all five clay-to-pot steps on this page. They appear in order on the product page.</p>
        </div>
        <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="btn btn-outline-secondary">
            View product
        </a>
    </div>

    <form method="POST" action="{{ route('admin.products.journey.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            @foreach ($steps as $index => $step)
                @php
                    $stepNumber = $index + 1;
                    $oldTitle = old("steps.{$index}.title", $step['title']);
                    $oldText = old("steps.{$index}.text", $step['text']);
                @endphp
                <div class="col-12">
                    <div class="admin-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="badge text-bg-dark rounded-pill px-3 py-2">Step {{ $stepNumber }}</span>
                            <h2 class="h5 mb-0">Journey step {{ $stepNumber }}</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="steps_{{ $index }}_title">Title</label>
                                <input type="text"
                                    name="steps[{{ $index }}][title]"
                                    id="steps_{{ $index }}_title"
                                    class="form-control @error("steps.{$index}.title") is-invalid @enderror"
                                    value="{{ $oldTitle }}"
                                    maxlength="120"
                                    required
                                    placeholder="e.g. Clay, Shape, Dry, Fire, Finish">
                                @error("steps.{$index}.title")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="steps_{{ $index }}_text">Description</label>
                                <textarea name="steps[{{ $index }}][text]"
                                    id="steps_{{ $index }}_text"
                                    class="form-control @error("steps.{$index}.text") is-invalid @enderror"
                                    rows="3"
                                    maxlength="1000"
                                    required
                                    placeholder="Describe this stage of the making process.">{{ $oldText }}</textarea>
                                @error("steps.{$index}.text")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="steps_{{ $index }}_image">Image <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="file"
                                    name="steps[{{ $index }}][image]"
                                    id="steps_{{ $index }}_image"
                                    class="form-control @error("steps.{$index}.image") is-invalid @enderror"
                                    accept="image/*">
                                @error("steps.{$index}.image")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($step['imageUrl'])
                                    <div class="mt-2">
                                        <img src="{{ $step['imageUrl'] }}" alt="{{ $oldTitle ?: 'Step '.$stepNumber }}" class="rounded border" width="160" height="120" style="object-fit: cover;">
                                        <div class="form-text">Upload a new file to replace the current image.</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn btn-dark">Save journey</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Back to products</a>
        </div>
    </form>
@endsection
