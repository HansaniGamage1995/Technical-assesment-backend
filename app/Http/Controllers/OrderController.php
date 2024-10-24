<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.product')->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $order = Order::create($request->only(['user_id', 'total_price', 'status']));

        foreach ($request->order_items as $item) {
            $order->orderItems()->create($item);
        }

        return response()->json($order, 201);
    }

    public function show(Order $order)
    {
        return response()->json($order->load('orderItems.product'));
    }

    public function update(Request $request, Order $order)
    {
        $order->update($request->only(['status']));

        return response()->json($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }
}
