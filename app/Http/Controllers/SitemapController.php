<?php

namespace App\Http\Controllers;

use App\Domain\Blog\Models\Blog;
use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * XML sitemap of public storefront pages.
     */
    public function index(): Response
    {
        $entries = array_merge(
            $this->staticPages(),
            [
                ['loc' => route('shop'), 'priority' => '0.9', 'changefreq' => 'weekly'],
                ['loc' => route('blog.index'), 'priority' => '0.6', 'changefreq' => 'weekly'],
            ],
            $this->modelEntries(
                Product::query()->whereHas('category'),
                'product.show',
                '0.8',
                'weekly'
            ),
            $this->modelEntries(Category::query(), 'category.show', '0.7', 'weekly'),
            $this->modelEntries(Brand::query(), 'brand.show', '0.5', 'monthly'),
            $this->modelEntries(Blog::query(), 'blog.show', '0.6', 'monthly'),
        );

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Plain-text robots.txt declaring the sitemap location.
     */
    public function robots(): Response
    {
        return response()
            ->view('robots', [], 200, ['Content-Type' => 'text/plain']);
    }

    /**
     * Static public pages: home, about, and policy pages.
     *
     * @return array<int, array{loc: string, priority: string, changefreq: string}>
     */
    private function staticPages(): array
    {
        return [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => route('about'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('faqs'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('terms'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => route('privacy'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => route('refund'), 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];
    }

    /**
     * Build sitemap entries from slugged models.
     *
     * @param  Builder<Model>  $query
     * @return array<int, array{loc: string, priority: string, changefreq: string}>
     */
    private function modelEntries(Builder $query, string $routeName, string $priority, string $changefreq): array
    {
        return $query
            ->get()
            ->map(fn ($model): array => [
                'loc' => route($routeName, $model->slug),
                'priority' => $priority,
                'changefreq' => $changefreq,
            ])
            ->all();
    }
}
