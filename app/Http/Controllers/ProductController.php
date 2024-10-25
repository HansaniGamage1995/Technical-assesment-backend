<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
        ]);

        $product->update($validatedData);

        return response()->json($product);
    }

    public function index(Request $request)
    {
        $pageSize = (int) $request->input('page_size', 10);
        $page = (int) $request->input('page', 1);
        $search = $request->input('search');
        $productsQuery = Product::GetAllProdcuts();
        if (!empty($search)) {
            $productsQuery->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
    
        $productCount = $productsQuery->count();
        $lastPage = (int) ceil($productCount / $pageSize);
        if ($page > $lastPage && $lastPage > 0) {
            return response()->json([
                'data' => [],
                'total' => $productCount,
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $pageSize,
                'from' => null,
                'to' => null,
            ]);
        }
    
        $products = $productsQuery->paginate($pageSize, ['*'], 'page', $page);
    
        return response()->json([
            'data' => $products->items(),
            'total' => $productCount,
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem(),
        ]);
    }


    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
