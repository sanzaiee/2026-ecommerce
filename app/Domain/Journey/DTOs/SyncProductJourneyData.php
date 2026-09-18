<?php

namespace App\Domain\Journey\DTOs;

use Illuminate\Http\UploadedFile;

readonly class SyncProductJourneyData
{
    public const STEP_COUNT = 5;

    /**
     * @param  list<array{title: string, text: string, image: ?UploadedFile}>  $steps
     */
    public function __construct(
        public int $productId,
        public array $steps,
    ) {}
}
