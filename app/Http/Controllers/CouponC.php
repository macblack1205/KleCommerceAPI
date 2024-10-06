<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CouponRequest;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponC extends Controller
{
    public function index()
    {
        $coupons = Coupon::all();
        return $coupons ? response()->json($coupons, 200) : response()->json(['message' =>'No coupons found'], 404);

    }

    public function store(CouponRequest $request)
    {
        $data = $request->validated();
        $coupon = Coupon::create(
            array_merge($data, ['user_id' => Auth::id()])
        );
        return response()->json(['message'=>'Coupon has been added','coupon'=>$coupon], 201);
    }

    public function show($id)
    {
        $coupon = Coupon::find($id);
        return $coupon ? response()->json($coupon, 200) : response()->json(['message' =>'No coupon found'], 404);
    }

    public function update(CouponRequest $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        
        if ($request->user()->id !== $coupon->user_id)
            return response()->json(['message' => 'Unauthorized'], 403);

        $data = $request->validated();    
        $coupon->update($data);
        return response()->json($coupon, 200);
    }

    public function destroy(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        if ($request->user()->id !== $coupon->user_id)
            return response()->json(['message' => 'Unauthorized'], 403);
        $coupon->delete();
        return response()->json(null, 204);
    }
}
