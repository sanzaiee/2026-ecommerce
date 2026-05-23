<?php

use App\Domain\CMS\Models\AboutPage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = AboutPage::query()->find(1);

        if (! $page) {
            return;
        }

        foreach (['gallery' => 3, 'craft' => 2] as $collection => $slotCount) {
            $items = $page->getMedia($collection)->sortBy('order_column')->values();

            foreach ($items as $index => $media) {
                if ($media->getCustomProperty('slot') !== null) {
                    continue;
                }

                if ($index >= $slotCount) {
                    continue;
                }

                $media->setCustomProperty('slot', $index);
                $media->save();
            }
        }
    }

    public function down(): void
    {
        // No rollback — slot metadata is safe to keep.
    }
};
