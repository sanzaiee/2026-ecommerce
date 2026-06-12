<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Customer\DTOs\CustomerFilterData;
use App\Domain\Customer\Services\CustomerAdminService;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BanCustomerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private CustomerAdminService $customers) {}

    public function index(Request $request): View
    {
        $filters = new CustomerFilterData(
            search: $request->string('search')->toString() ?: null,
            isBanned: $request->has('is_banned') ? $request->boolean('is_banned') : null,
            hasOrders: $request->has('has_orders') ? $request->boolean('has_orders') : null,
            orderBy: $request->string('sorted_by')->toString() ?: null,
            orderDirection: $request->string('direction')->toString() ?: 'desc',
            perPage: 20,
            page: max(1, (int) $request->input('page', 1)),
        );

        return view('admin.customers.index', [
            'customers' => $this->customers->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function show(int $customer): View
    {
        $customer = $this->customers->find($customer);

        return view('admin.customers.show', compact('customer'));
    }

    public function ban(BanCustomerRequest $request, int $customer): RedirectResponse
    {
        $customer = $this->customers->find($customer);
        $this->customers->ban($customer, $request->validated('reason'));

        return back()->with('status', 'Customer banned successfully.');
    }

    public function unban(int $customer): RedirectResponse
    {
        $customer = $this->customers->find($customer);
        $this->customers->unban($customer);

        return back()->with('status', 'Customer unbanned successfully.');
    }
}