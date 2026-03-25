<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function listUserOrders(User $user): Collection
    {
        return $this->userOrdersQuery($user)
            ->latest()
            ->get();
    }

    public function getUserOrder(User $user, int|string $identifier): Order
    {
        return $this->userOrderByIdentifierQuery($user, $identifier)
            ->with('items.product')
            ->firstOrFail();
    }

    /**
     * @throws ValidationException
     */
    public function payUserOrder(User $user, int|string $identifier): Order
    {
        return DB::transaction(function () use ($user, $identifier) {
            $order = $this->userOrderByIdentifierQuery($user, $identifier)
                ->lockForUpdate()
                ->firstOrFail();

            return $this->payOrder($order);
        });
    }

    /**
     * @param array<int, array{id:int, quantity:int}> $productsPayload
     *
     * @throws ValidationException
     */
    public function createOrder(User $user, array $productsPayload, string $currency = 'EUR'): Order
    {
        return DB::transaction(function () use ($user, $productsPayload, $currency) {
            $productIds = collect($productsPayload)
                ->pluck('id')
                ->unique()
                ->values();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $missingProductIds = $productIds->diff($products->keys());
            if ($missingProductIds->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'products' => ['Uno o piu prodotti non esistono.'],
                ]);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => 0,
                'currency' => $currency,
                'status' => OrderStatus::Pending,
            ]);

            $orderItems = [];
            $totalPrice = 0.0;

            foreach ($productsPayload as $item) {
                $product = $products->get($item['id']);
                $quantity = (int) $item['quantity'];

                if ($product->stock < $quantity) {
                    Log::warning('Order creation failed: insufficient stock.', [
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'requested_quantity' => $quantity,
                        'available_stock' => $product->stock,
                    ]);

                    throw ValidationException::withMessages([
                        'products' => ["Stock insufficiente per il prodotto ID {$product->id}."],
                    ]);
                }

                $linePrice = (float) $product->price;
                $totalPrice += $linePrice * $quantity;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $linePrice,
                ];

                $product->decrement('stock', $quantity);
            }

            $order->items()->createMany($orderItems);
            $order->update([
                'total_price' => round($totalPrice, 2),
            ]);

            $createdOrder = $order->load('items.product');

            Log::info('Order created.', [
                'order_id' => $createdOrder->id,
                'order_uuid' => $createdOrder->uuid,
                'user_id' => $createdOrder->user_id,
                'total_price' => $createdOrder->total_price,
                'currency' => $createdOrder->currency,
            ]);

            event(new OrderCreated($createdOrder));

            return $createdOrder;
        });
    }

    /**
     * @throws ValidationException
     */
    public function payOrder(Order $order): Order
    {
        if ($order->status === OrderStatus::Paid) {
            Log::warning('Order payment failed: order already paid.', [
                'order_id' => $order->id,
                'order_uuid' => $order->uuid,
                'status' => $order->status?->value ?? $order->status,
            ]);

            throw ValidationException::withMessages([
                'order' => ['Ordine gia pagato.'],
            ]);
        }

        if ($order->status !== OrderStatus::Pending) {
            Log::warning('Order payment failed: invalid order status.', [
                'order_id' => $order->id,
                'order_uuid' => $order->uuid,
                'status' => $order->status?->value ?? $order->status,
            ]);

            throw ValidationException::withMessages([
                'order' => ['Solo gli ordini in stato pending possono essere pagati.'],
            ]);
        }

        $order->update([
            'status' => OrderStatus::Paid,
        ]);

        $paidOrder = $order->refresh()->load('items.product');

        Log::info('Order paid.', [
            'order_id' => $paidOrder->id,
            'order_uuid' => $paidOrder->uuid,
            'user_id' => $paidOrder->user_id,
            'total_price' => $paidOrder->total_price,
            'currency' => $paidOrder->currency,
        ]);

        event(new OrderPaid($paidOrder));

        return $paidOrder;
    }

    private function userOrdersQuery(User $user): HasMany
    {
        return $user->orders()->with('items.product');
    }

    private function userOrderByIdentifierQuery(User $user, int|string $identifier): HasMany
    {
        $query = $user->orders();

        if (is_int($identifier) || ctype_digit((string) $identifier)) {
            return $query->where('id', (int) $identifier);
        }

        return $query->where('uuid', (string) $identifier);
    }
}
