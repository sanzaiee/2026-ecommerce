<?php

namespace App\Domain\CMS\Repositories;

use App\Domain\CMS\Models\AboutPage;

interface AboutPageRepositoryInterface
{
    public function getSingleton(): AboutPage;

    public function update(AboutPage $aboutPage, array $attributes): AboutPage;
}
