<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // 1. Create a new product
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

    // 2. Update an existing product
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

    // 3. List all products with search and pagination
    public function index(Request $request)
    {
        // Get 'page_size' from query parameters, default to 10 if not provided
        $pageSize = (int) $request->input('page_size', 10);
    
        // Get the 'page' parameter from query, default to 1 if not provided
        $page = (int) $request->input('page', 1);
    
        // Get the search query from request
        $search = $request->input('search');
    
        // Get the products query with any required relationships or filters
        $productsQuery = Product::query();
    
        // Apply search filter if 'search' parameter is provided
        if (!empty($search)) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }
    
        // Get the total product count after filtering
        $productCount = $productsQuery->count();
    
        // Ensure the requested page is within the valid range
        $lastPage = (int) ceil($productCount / $pageSize);
        if ($page > $lastPage && $lastPage > 0) {
            // If the requested page is out of range, return an empty response
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
    
        // Apply pagination to the query
        $products = $productsQuery->paginate($pageSize, ['*'], 'page', $page);
    
        // Customize the response to include pagination data along with the products
        return response()->json([
            'data' => $products->items(), // The paginated products
            'total' => $productCount,     // Total products count
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

    // 4. Soft-delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Soft-delete the product

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
