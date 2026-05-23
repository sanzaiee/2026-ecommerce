<?php

namespace App\Http\Controllers;

use App\Domain\CMS\Services\CMSService;
use App\Domain\Testimonial\Services\TestimonialService;
use App\Support\ViewData\LandingPageMapper;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private CMSService $cms,
        private LandingPageMapper $mapper,
        private TestimonialService $testimonials,
    ) {}

    public function __invoke(): View
    {
        $page = $this->cms->getPublicPayload();
        $data = $this->mapper->toHomeView(
            $page,
            $this->cms->everydayProducts($page),
            $this->cms->topSellingProducts($page),
        );

        return view('landing', array_merge($data, [
            'cartTotal' => 'Rs. 0',
            'testimonials' => $this->testimonials->forHome(6),
        ]));
    }
}
