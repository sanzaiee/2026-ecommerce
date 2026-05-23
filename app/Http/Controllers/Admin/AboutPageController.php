<?php

namespace App\Http\Controllers\Admin;

use App\Domain\CMS\Services\AboutPageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\UpdateAboutPageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function __construct(private AboutPageService $aboutPage) {}

    public function edit(): View
    {
        $page = $this->aboutPage->getPublicPayload();

        $galleryMedia = collect(range(0, 2))
            ->mapWithKeys(fn (int $slot) => [$slot => $page->mediaForSlot('gallery', $slot)]);

        $craftMedia = collect(range(0, 1))
            ->mapWithKeys(fn (int $slot) => [$slot => $page->mediaForSlot('craft', $slot)]);

        return view('admin.about-page.edit', [
            'page' => $page,
            'galleryMedia' => $galleryMedia,
            'craftMedia' => $craftMedia,
        ]);
    }

    public function update(UpdateAboutPageRequest $request): RedirectResponse
    {
        $this->aboutPage->update($request->toDto());

        return redirect()->route('admin.about-page.edit')
            ->with('status', 'About page updated successfully.');
    }
}
