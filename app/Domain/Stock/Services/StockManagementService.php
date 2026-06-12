<?php

namespace App\Domain\Stock\Services;

use App\Domain\Order\Models\Order;
use App\Domain\Order\Models\OrderItem;
use App\Domain\Product\Models\Product;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class StockManagementService
{
    public function canReserve(Order $order): bool
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if (! $product) {
                continue;
            }

            if ($product->stock_quantity < $item->quantity) {
                return false;
            }
        }

        return true;
    }

    public function reserveStock(Order $order): void
    {
        if (! $this->canReserve($order)) {
            $insufficientItems = collect($order->items)
                ->filter(fn ($item) => $item->product && $item->product->stock_quantity < $item->quantity)
                ->map(function ($item) {
                    $availableStock = $item->product?->stock_quantity ?? 0;

                    return "{$item->product_title} (requested: {$item->quantity}, available: {$availableStock})";
                })
                ->join(', ');

            throw ValidationException::withMessages([
                'stock' => ['Insufficient stock for: '.$insufficientItems],
            ]);
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = $item->product;

                if (! $product) {
                    continue;
                }

                $product->decrement('stock_quantity', $item->quantity);
            }

            Log::info('Stock reserved for order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        });
    }

    public function releaseStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = $item->product;

                if (! $product) {
                    continue;
                }

                $product->increment('stock_quantity', $item->quantity);
            }

            Log::info('Stock released for order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        });
    }

    public function checkAvailability(Product $product, int $requestedQuantity): bool
    {
        return $product->hasStockFor($requestedQuantity);
    }

    public function getAvailableStock(Product $product): int
    {
        return max(0, $product->stock_quantity);
    }

    public function updateStock(Product $product, int $newQuantity): void
    {
        if ($newQuantity < 0) {
            throw new \InvalidArgumentException('Stock quantity cannot be negative.');
        }

        $product->update([
            'stock_quantity' => $newQuantity,
        ]);

        Log::info('Stock updated', [
            'product_id' => $product->id,
            'product_title' => $product->title,
            'new_quantity' => $newQuantity,
        ]);
    }

    public function adjustStock(Product $product, int $adjustment): void
    {
        $currentQuantity = $product->stock_quantity;
        $newQuantity = max(0, $currentQuantity + $adjustment);

        $product->update([
            'stock_quantity' => $newQuantity,
        ]);

        Log::info('Stock adjusted', [
            'product_id' => $product->id,
            'product_title' => $product->title,
            'previous_quantity' => $currentQuantity,
            'adjustment' => $adjustment,
            'new_quantity' => $newQuantity,
        ]);
    }
}