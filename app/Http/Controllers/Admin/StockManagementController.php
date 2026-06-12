<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Product\Models\Product;
use App\Domain\Stock\Services\StockManagementService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockManagementController extends Controller
{
    public function __construct(
        private StockManagementService $stock,
    ) {}

    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $stockStatus = $request->string('stock_status')->toString();

        $query = Product::query()->with(['category', 'brand']);

        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($stockStatus === 'low') {
            $query->Where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 10);
        } elseif ($stockStatus === 'out') {
            $query->where('stock_quantity', 0);
        }

        $products = $query->orderBy('stock_quantity', 'asc')
            ->paginate(20);

        return view('admin.stock.index', compact('products', 'search', 'stockStatus'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $this->stock->updateStock($product, (int) $validated['stock_quantity']);

        return back()->with('status', 'Stock updated successfully.');
    }

    public function adjust(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'adjustment' => 'required|integer',
        ]);

        $this->stock->adjustStock($product, (int) $validated['adjustment']);

        return back()->with('status', 'Stock adjusted successfully.');
    }
}