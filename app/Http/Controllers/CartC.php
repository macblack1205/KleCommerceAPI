<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartC extends Controller
{
    public function index()
    {
        $cart = Cart::with(['products', 'user', 'coupon'])->where('user_id', Auth::id())->firstOrFail();
        return response()->json($cart);
    }


    public function add($id)
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $product = Product::findOrFail($id);
        $cart->products()->attach($product->id); 
        return response()->json(['cart' =>$cart->load('products')], 201);
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::where('user_id', Auth::id())->firstOrFail();
        $cart->products()->updateExistingPivot($id, ['quantity' => $request->quantity]);
        return response()->json(['message' => 'Cart updated successfully', 'cart' => $cart->load('products')]);
    }

    public function remove($id)
    {
        $cart = Cart::where('user_id', Auth::id())->firstOrFail();
        $cart->products()->detach($id);
        return response()->json($cart->load('products'));
    }

    public function applyCoupon(Request $request)
    {
        $couponCode = $request->coupon;
    
        $cart = Cart::where('user_id', Auth::id())->firstOrFail();
        $coupon = Coupon::where('coupon', $couponCode)->first();
    
        if (!$coupon) {
            return response()->json(['message' => 'Invalid coupon code'], 404);
        }
    
        $cart->coupon_id = $coupon->id;
        $cart->save();
    
        return response()->json(['message' => 'Coupon applied successfully', 'cart' => $cart->load('products', 'coupon')]);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->products()->detach(); // Remove all product associations
        $cart->delete();
        return response()->json(null, 204);
    }
}
