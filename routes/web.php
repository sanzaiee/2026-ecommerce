<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AboutPageController as AdminAboutPageController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LandingPageController as AdminLandingPageController;
use App\Http\Controllers\Admin\NewsletterSubscriberController as AdminNewsletterSubscriberController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\StockManagementController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Store\CartController as StoreCartController;
use App\Http\Controllers\Store\CheckoutController as StoreCheckoutController;
use App\Http\Controllers\Store\WishlistController as StoreWishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/shop', ShopController::class)->name('shop');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{slug}/reviews', [ProductController::class, 'storeReview'])->name('product.reviews.store');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/brand/{slug}', [BrandController::class, 'show'])->name('brand.show');

Route::redirect('/products/{slug}', '/product/{slug}')->where('slug', '[a-z0-9\-]+');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'customer'])->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{orderNumber}', [AccountController::class, 'showOrder'])->name('account.orders.show');
    Route::get('/account/orders/{orderNumber}/invoice', [AccountController::class, 'orderInvoice'])->name('account.orders.invoice');
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
    Route::put('/account/addresses', [AccountController::class, 'updateAddresses'])->name('account.addresses.update');
    Route::get('/account/wishlist', [AccountController::class, 'wishlist'])->name('account.wishlist');
});

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit')->middleware('throttle:5,1');
Route::post('/newsletter', [PageController::class, 'subscribeNewsletter'])->name('newsletter.subscribe')->middleware('throttle:5,1');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show')->where('slug', '[a-z0-9\-]+');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faqs', [PageController::class, 'faq'])->name('faqs');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/refund-policy', [PageController::class, 'refund'])->name('refund');

Route::prefix('store')->name('store.')->group(function () {
    Route::get('/cart', [StoreCartController::class, 'show'])->name('cart.show');
    Route::post('/cart/items', [StoreCartController::class, 'store'])->name('cart.items.store');
    Route::post('/cart/sync', [StoreCartController::class, 'sync'])->name('cart.sync');
    Route::patch('/cart/items/{productSlug}', [StoreCartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{productSlug}', [StoreCartController::class, 'destroy'])->name('cart.items.destroy');

    Route::get('/wishlist', [StoreWishlistController::class, 'show'])->name('wishlist.show');
    Route::post('/wishlist/items', [StoreWishlistController::class, 'store'])->name('wishlist.items.store');
    Route::post('/wishlist/sync', [StoreWishlistController::class, 'sync'])->name('wishlist.sync');
    Route::delete('/wishlist/items/{productSlug}', [StoreWishlistController::class, 'destroy'])->name('wishlist.items.destroy');
    Route::post('/wishlist/items/{productSlug}/move-to-cart', [StoreWishlistController::class, 'moveToCart'])
        ->name('wishlist.items.move');
    Route::post('/wishlist/move-all-to-cart', [StoreWishlistController::class, 'moveAllToCart'])->name('wishlist.move-all');

    Route::middleware(['auth', 'customer'])->group(function () {
        Route::get('/checkout', [StoreCheckoutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout', [StoreCheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/success/{orderNumber}', [StoreCheckoutController::class, 'success'])->name('checkout.success');
        Route::get('/checkout/failure/{orderNumber}', [StoreCheckoutController::class, 'failure'])->name('checkout.failure');
        Route::get('/checkout/payment/{orderNumber}', [StoreCheckoutController::class, 'payment'])->name('checkout.payment');
        Route::get('/checkout/payment/{orderNumber}/success', [StoreCheckoutController::class, 'paymentSuccess'])
            ->name('checkout.payment.success');
        Route::get('/checkout/payment/{orderNumber}/failure', [StoreCheckoutController::class, 'paymentFailure'])
            ->name('checkout.payment.failure');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::delete('products/{product}/media/{media}', [AdminProductController::class, 'destroyMedia'])
            ->name('products.media.destroy');
        Route::resource('products', AdminProductController::class)->except(['show']);

        Route::delete('categories/{category}/image', [AdminCategoryController::class, 'destroyImage'])
            ->name('categories.image.destroy');
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        Route::delete('brands/{brand}/image', [AdminBrandController::class, 'destroyImage'])
            ->name('brands.image.destroy');
        Route::resource('brands', AdminBrandController::class)->except(['show']);
        Route::get('/landing-page', [AdminLandingPageController::class, 'edit'])->name('landing-page.edit');
        Route::put('/landing-page', [AdminLandingPageController::class, 'update'])->name('landing-page.update');
        Route::get('/about-page', [AdminAboutPageController::class, 'edit'])->name('about-page.edit');
        Route::put('/about-page', [AdminAboutPageController::class, 'update'])->name('about-page.update');

        Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::patch('/orders/{order}/delivery-status', [AdminOrderController::class, 'updateDeliveryStatus'])->name('orders.delivery-status');
        Route::patch('/orders/{order}/mark-paid', [AdminOrderController::class, 'markPaid'])->name('orders.mark-paid');

        Route::resource('testimonials', AdminTestimonialController::class)->except(['show']);
        Route::delete('blogs/{blog}/image', [AdminBlogController::class, 'destroyImage'])
            ->name('blogs.image.destroy');
        Route::resource('blogs', AdminBlogController::class)->except(['show']);

        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{id}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::patch('/contact-messages/{contactMessage}/read', [AdminContactMessageController::class, 'markAsRead'])->name('contact-messages.read');
        Route::delete('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

        Route::get('/newsletter-subscribers', [AdminNewsletterSubscriberController::class, 'index'])->name('newsletter-subscribers.index');
        Route::delete('/newsletter-subscribers/{newsletterSubscriber}', [AdminNewsletterSubscriberController::class, 'destroy'])->name('newsletter-subscribers.destroy');

        Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::patch('/customers/{customer}/ban', [AdminCustomerController::class, 'ban'])->name('customers.ban');
        Route::patch('/customers/{customer}/unban', [AdminCustomerController::class, 'unban'])->name('customers.unban');

        Route::get('/stock', [StockManagementController::class, 'index'])->name('stock.index');
        Route::patch('/stock/{product}/update', [StockManagementController::class, 'update'])->name('stock.update');
        Route::patch('/stock/{product}/adjust', [StockManagementController::class, 'adjust'])->name('stock.adjust');
    });
});
