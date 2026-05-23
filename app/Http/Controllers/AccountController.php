<?php

namespace App\Http\Controllers;

use App\Domain\Order\Models\Order;
use App\Domain\Wishlist\Models\WishlistItem;
use App\Domain\Wishlist\Services\WishlistService;
use App\Http\Requests\Account\UpdateAddressRequest;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Models\User;
use App\Support\Store\StoreOwnerResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        private WishlistService $wishlist,
        private StoreOwnerResolver $ownerResolver,
    ) {}

    public function index(Request $request): View
    {
        $user = $this->customer($request);

        $orders = $this->ordersQuery($user)->limit(3)->get();

        return $this->view('account.dashboard', [
            'pageTitle' => 'Dashboard',
            'orders' => $orders,
        ]);
    }

    public function orders(Request $request): View
    {
        $user = $this->customer($request);

        $orders = $this->ordersQuery($user)->paginate(10);

        return $this->view('account.orders.index', [
            'pageTitle' => 'Orders',
            'orders' => $orders,
        ]);
    }

    public function showOrder(Request $request, string $orderNumber): View
    {
        $user = $this->customer($request);

        $order = Order::query()
            ->where('user_id', $user->id)
            ->where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        return $this->view('account.orders.show', [
            'pageTitle' => 'Order '.$order->order_number,
            'breadcrumbSection' => 'Orders',
            'order' => $order,
        ]);
    }

    public function profile(Request $request): View
    {
        $this->customer($request);

        return $this->view('account.profile', [
            'pageTitle' => 'Profile & security',
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $this->customer($request);

        $user->update([
            'name' => $request->string('name')->toString(),
        ]);

        return redirect()
            ->route('account.profile')
            ->with('status', 'Your profile has been updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $this->customer($request);

        $user->update([
            'password' => $request->string('password')->toString(),
        ]);

        return redirect()
            ->route('account.profile')
            ->with('status', 'Your password has been updated.');
    }

    public function addresses(Request $request): View
    {
        $this->customer($request);

        return $this->view('account.addresses', [
            'pageTitle' => 'Saved addresses',
        ]);
    }

    public function updateAddresses(UpdateAddressRequest $request): RedirectResponse
    {
        $user = $this->customer($request);

        $user->update([
            'phone' => $request->string('phone')->toString(),
            'shipping_address_line1' => $request->string('shipping_address_line1')->toString(),
            'shipping_address_line2' => $request->filled('shipping_address_line2')
                ? $request->string('shipping_address_line2')->toString()
                : null,
            'shipping_city' => $request->string('shipping_city')->toString(),
            'shipping_district' => $request->string('shipping_district')->toString(),
            'shipping_postal_code' => $request->filled('shipping_postal_code')
                ? $request->string('shipping_postal_code')->toString()
                : null,
        ]);

        return redirect()
            ->route('account.addresses')
            ->with('status', 'Your delivery address has been saved.');
    }

    public function wishlist(Request $request): View
    {
        $this->customer($request);

        $wishlistItems = $this->wishlist->paginate(
            $this->ownerResolver->fromRequest($request),
            perPage: 5,
        );

        return $this->view('account.wishlist', [
            'pageTitle' => 'Wishlist',
            'wishlistItems' => $wishlistItems,
        ]);
    }

    private function customer(Request $request): User
    {
        $user = $request->user();

        abort_unless($user?->isCustomer(), 403);

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function view(string $view, array $data = []): View
    {
        $user = request()->user();

        return view($view, array_merge([
            'user' => $user,
            'orderCount' => Order::query()->where('user_id', $user->id)->count(),
            'wishlistCount' => WishlistItem::query()->where('user_id', $user->id)->count(),
            'cartTotal' => 'Rs. 0',
        ], $data));
    }

    /**
     * @return Builder<Order>
     */
    private function ordersQuery(User $user)
    {
        return Order::query()
            ->where('user_id', $user->id)
            ->with('items')
            ->latest('placed_at');
    }
}
