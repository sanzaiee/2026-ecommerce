<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Testimonial\DTOs\TestimonialFilterData;
use App\Domain\Testimonial\Services\TestimonialService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Testimonial\StoreTestimonialRequest;
use App\Http\Requests\Testimonial\UpdateTestimonialRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function __construct(private TestimonialService $testimonials) {}

    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);

        return view('admin.testimonials.index', [
            'testimonials' => $this->testimonials->paginateForAdmin($filters),
            'filters' => $filters,
        ]);
    }

    private function filtersFromRequest(Request $request): TestimonialFilterData
    {
        $search = trim($request->string('search')->toString());
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();
        $published = $request->string('published')->toString();
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

        return new TestimonialFilterData(
            search: $search !== '' ? $search : null,
            published: in_array($published, ['published', 'draft'], true) ? $published : null,
            dateFrom: $dateFrom !== '' ? $dateFrom : null,
            dateTo: $dateTo !== '' ? $dateTo : null,
            perPage: $perPage,
            page: max(1, (int) $request->input('page', 1)),
        );
    }

    public function create(): View
    {
        return view('admin.testimonials.form', [
            'testimonial' => null,
            'products' => $this->testimonials->productOptionsForForm(),
        ]);
    }

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        $this->testimonials->create($request->toDto());

        return redirect()->route('admin.testimonials.index')
            ->with('status', 'Testimonial created successfully.');
    }

    public function edit(int $testimonial): View
    {
        $model = $this->testimonials->findById($testimonial);

        if (! $model) {
            abort(404);
        }

        return view('admin.testimonials.form', [
            'testimonial' => $model,
            'products' => $this->testimonials->productOptionsForForm(),
        ]);
    }

    public function update(UpdateTestimonialRequest $request, int $testimonial): RedirectResponse
    {
        $this->testimonials->update($request->toDto());

        return redirect()->route('admin.testimonials.index')
            ->with('status', 'Testimonial updated successfully.');
    }

    public function destroy(int $testimonial): RedirectResponse
    {
        $this->testimonials->delete($testimonial);

        return redirect()->route('admin.testimonials.index')
            ->with('status', 'Testimonial deleted.');
    }
}
