<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Settings\Services\SettingsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSiteSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private SettingsService $settings,
    ) {}

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => $this->settings->getSingleton(),
        ]);
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        $this->settings->update($request->toDto());

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Site settings updated successfully.');
    }
}
