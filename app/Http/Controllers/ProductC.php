<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
// use App\HttpResponse;

class ProductC extends Controller
{
    // use HttpResponse;
    public function index(Request $request)
    {
        $products = Product::with(['user', 'reviews'])->get();
    
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $products = $products->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
    
        $products->each(function($product) {
            $product->average_rating = $product->reviews->avg('rating') ?: 0;
            $product->image_url = $product->getImageUrlAttribute();
        });
    
        return $products->isNotEmpty()
            ? response()->json($products, 200)
            : response()->json(['message' => 'No products found'], 404);
    }
    
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $path = $request->image->storeAs('photos', $imageName, 'public');
            $data['image'] = $path;  
        }else {
            // Default image path if no image is uploaded
            $data['image'] = 'storage/bag.jpg';
        }
        $product = Product::create(
            array_merge($data, ['user_id' => Auth::id()])
        );
        return response()->json(['message'=>'Product has been added','product'=>$product], 201);

    }

    public function show($id)
    {
        $product = Product::with(['user', 'reviews'])->find($id);

        if (!$product) return response()->json(['message' => 'No product found'], 404);

        $product->append(['image_url', 'average_rating']);
        $seller = $product->user;
        $sellerProducts = $seller->products()->with('reviews')->get();
        $sellerProducts->each(function ($prod) {
            $prod->average_rating = $prod->reviews->avg('rating') ?: 0;
        });
        $topRatedProduct = $sellerProducts->sortByDesc('average_rating')->first();
        return response()->json([
            'product' => $product,
            'seller' => $seller,
            'topRatedProduct' => $topRatedProduct,
        ]);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product || $product->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validated();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('photos', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    public function destroy($id)
    {
         $product = Product::find($id);

        if (!$product || $product->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
