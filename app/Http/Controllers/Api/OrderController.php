<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(CreateOrderRequest $request, OrderService $orderService)
    {
        $validated = $request->validated();

        $order = $orderService->createOrder(
            user: $request->user(),
            productsPayload: $validated['products'],
            currency: $validated['currency'] ?? 'EUR',
        );

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request, OrderService $orderService)
    {
        $orders = $orderService->listUserOrders($request->user());

        return OrderResource::collection($orders);
    }

    public function show(Request $request, string $id, OrderService $orderService)
    {
        $order = $orderService->getUserOrder($request->user(), $id);

        return new OrderResource($order);
    }

    public function pay(Request $request, string $id, OrderService $orderService)
    {
        $paidOrder = $orderService->payUserOrder($request->user(), $id);

        return response()->json([
            'message' => 'Pagamento riuscito',
            'data' => new OrderResource($paidOrder),
        ]);
    }
}
