<?php

namespace App\Http\Controllers;

use App\Domain\Blog\Models\Blog;
use App\Domain\Blog\Services\BlogService;
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
        private BlogService $blogs,
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
            'stories' => $this->storyCards(),
        ]));
    }

    /**
     * Latest blog posts prepared for the "Stories From the Tradition" section.
     *
     * @return array<int, array<string, mixed>>
     */
    private function storyCards(): array
    {
        return $this->blogs
            ->latestForStorefront(3)
            ->map(fn (Blog $post): array => [
                'title' => $post->title,
                'excerpt' => $post->summary(140),
                'image' => $post->hasImage() ? $post->imageUrl('medium') : null,
                'category' => $post->category?->name,
                'date' => $post->created_at?->format('M j, Y'),
                'url' => route('blog.show', $post->slug),
            ])
            ->values()
            ->all();
    }
}
