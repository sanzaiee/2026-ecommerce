<?php

namespace App\Providers;

use App\Domain\Brand\Models\Brand;
use App\Domain\Brand\Repositories\BrandRepository;
use App\Domain\Brand\Repositories\BrandRepositoryInterface;
use App\Domain\Category\Models\Category;
use App\Domain\Category\Observers\CategoryObserver;
use App\Domain\Contact\Repositories\ContactMessageRepository;
use App\Domain\Contact\Repositories\ContactMessageRepositoryInterface;
use App\Domain\Newsletter\Repositories\NewsletterSubscriberRepository;
use App\Domain\Newsletter\Repositories\NewsletterSubscriberRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepository;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\CMS\Repositories\AboutPageRepository;
use App\Domain\CMS\Repositories\AboutPageRepositoryInterface;
use App\Domain\CMS\Repositories\LandingPageRepository;
use App\Domain\CMS\Repositories\LandingPageRepositoryInterface;
use App\Domain\Settings\Repositories\SiteSettingRepository;
use App\Domain\Settings\Repositories\SiteSettingRepositoryInterface;
use App\Domain\Settings\Services\SettingsService;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Observers\ProductObserver;
use App\Domain\Order\Repositories\OrderRepository;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepository;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Review\Repositories\ReviewRepository;
use App\Domain\Review\Repositories\ReviewRepositoryInterface;
use App\Domain\Testimonial\Models\Testimonial;
use App\Domain\Testimonial\Repositories\TestimonialRepository;
use App\Domain\Testimonial\Repositories\TestimonialRepositoryInterface;
use App\Domain\Customer\Repositories\CustomerRepository;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Events\CategoryUpdated;
use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Events\ReviewApproved;
use App\Listeners\ClearCategoryCache;
use App\Listeners\ClearProductCache;
use App\Listeners\UpdateProductRating;
use App\Domain\Category\Services\CategoryService;
use App\Domain\Wishlist\Services\WishlistService;
use App\Support\Store\StoreOwnerResolver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(TestimonialRepositoryInterface::class, TestimonialRepository::class);
        $this->app->bind(ContactMessageRepositoryInterface::class, ContactMessageRepository::class);
        $this->app->bind(NewsletterSubscriberRepositoryInterface::class, NewsletterSubscriberRepository::class);
        $this->app->bind(LandingPageRepositoryInterface::class, LandingPageRepository::class);
        $this->app->bind(AboutPageRepositoryInterface::class, AboutPageRepository::class);
        $this->app->bind(SiteSettingRepositoryInterface::class, SiteSettingRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);

        Event::listen(ProductCreated::class, ClearProductCache::class);
        Event::listen(ProductUpdated::class, ClearProductCache::class);
        Event::listen(ReviewApproved::class, ClearProductCache::class);
        Event::listen(ReviewApproved::class, UpdateProductRating::class);
        Event::listen(CategoryUpdated::class, ClearCategoryCache::class);

        Gate::policy(Product::class, \App\Policies\ProductPolicy::class);
        Gate::policy(Category::class, \App\Policies\CategoryPolicy::class);
        Gate::policy(Brand::class, \App\Policies\BrandPolicy::class);
        Gate::policy(\App\Domain\Review\Models\Review::class, \App\Policies\ReviewPolicy::class);
        Gate::policy(Testimonial::class, \App\Policies\TestimonialPolicy::class);

        View::composer([
            'shop',
            'landing',
            'category',
            'brand',
            'product',
            'partials.store.product-card-item',
        ], function ($view) {
            $owner = app(StoreOwnerResolver::class)->fromRequest(request());
            $view->with('wishlistSlugs', app(WishlistService::class)->productSlugs($owner));
        });

        View::composer('components.store.header', function ($view) {
            $categoryItems = app(CategoryService::class)->all()->map(fn ($c) => [
                'label' => $c->name,
                'href' => route('category.show', $c->slug),
            ])->all();

            $view->with('menuItems', array_merge([
                ['label' => 'All Products', 'href' => route('shop')],
            ], $categoryItems));
        });

        $siteComposer = function ($view) {
            $site = app(SettingsService::class)->getPublic();
            $view->with('site', $site);
        };

        View::composer([
            'layouts.store',
            'layouts.admin',
            'layouts.admin-guest',
            'components.store.header',
            'components.store.footer',
            'pages.*',
        ], $siteComposer);
    }
}
