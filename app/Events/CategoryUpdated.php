<?php

namespace App\Events;

use App\Domain\Category\Models\Category;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Category $category) {}
}
