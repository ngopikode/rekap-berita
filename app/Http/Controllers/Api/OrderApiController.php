<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderApiRequest;
use App\Models\Restaurant;
use App\Traits\ApiResponserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderApiController extends Controller
{
    use ApiResponserTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\OrderApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderApiRequest $request): JsonResponse
    {
        /** @var Restaurant $restaurant */
        $restaurant = $request->restaurant;

        try {
            $order = DB::transaction(function () use ($request, $restaurant) {
                $order = $restaurant->orders()->create([
                    'customer_name' => $request->customer_name,
                    'order_type' => $request->order_type,
                    'order_info' => $request->order_info,
                    'total_price' => $request->total_price,
                    'source' => $request->source,
                    'status' => 'pending',
                ]);

                foreach ($request->items as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }

                return $order;
            });

            return $this->successResponse($order, 'Order created successfully.', 201);

        } catch (Throwable $e) {
            return $this->errorResponse($e, 'Failed to create order.', 500, $request);
        }
    }
}
