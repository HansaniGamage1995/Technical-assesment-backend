<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = (int) $request->input('page_size', 10);
        $page = (int) $request->input('page', 1);
        $user = Auth::user();
        $isAdmin = $user->type == 'admin' ? true : false;
        $orders = Order::GetAllOrders($isAdmin, $user->id);
        $orderCount = $orders->count();
        $lastPage = (int) ceil($orderCount / $pageSize);
        if ($page > $lastPage && $lastPage > 0) {
            return response()->json([
                'data' => [],
                'total' => $orderCount,
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $pageSize,
                'from' => null,
                'to' => null,
            ]);
        }
        $orders = $orders->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'data' => $orders->items(), // The paginated products
            'total' => $orderCount,     // Total products count
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
            'from' => $orders->firstItem(),
            'to' => $orders->lastItem(),
        ]);
    }

    public function store(Request $request)
    {
        // Validate the input
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_price' => 'required|numeric|min:1',
            'status' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id', // Validate product ID
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Create the order
        $order = Order::create([
            'user_id' => $validatedData['user_id'],
            'total_price' => $validatedData['total_price'],
            'status' => $validatedData['status'],
        ]);

        // Create order items and update stock quantities
        foreach ($validatedData['items'] as $item) {
            $product = Product::find($item['product_id']);

            if ($product) {
                $product->stock_quantity -= $item['quantity'];
                $product->save();

                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        }

        return response()->json($order, 201);
    }


    public function show(Order $order)
    {
        return response()->json($order->load('orderItems.product'));
    }

    public function update(Request $request, Order $order)
    {
        $validatedData = $request->validate([
            'status' => 'required|string',
        ]);

        // dd($order);

        $order->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!',
            'order' => $order
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }
}
