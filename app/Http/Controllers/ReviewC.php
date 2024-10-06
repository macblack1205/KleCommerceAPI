<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;

class ReviewC extends Controller
{
    public function index($id)
    {
        $reviews = Review::where('product_id', $id)->with('user')->get();
        return $reviews ? response()->json($reviews, 200) : response()->json(['message' =>'No reviews found'], 404);
    }
    public function store(ReviewRequest $request)
    {
        $existingReview = Review::where('product_id', $request->product_id)
            ->where('user_id', Auth::id())->first();

        if ($existingReview) return response()->json(['message' => 'You have already reviewed this product.'], 403);
        $review = Review::create( array_merge($request->validated(), ['user_id' => Auth::id()]) );

        return response()->json(['message' => 'Review added successfully', 'review' => $review], 201);
    }
    

    public function show($id)
    {
        $review = Review::findOrFail($id);
        return $review ? response()->json($review, 200) : response()->json(['message' =>'No review found'], 404);
    }

    public function update(ReviewRequest $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update($request->validated());
        return response()->json(['message' => 'Review updated successfully', 'review' => $review], 200);
    }

    public function destroy($id)
    {
        Review::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
    public function getUserReview($id)
    {
        $review = Review::where('product_id', $id)
            ->where('user_id', Auth::id())->first();
        return $review ?  response()->json($review, 200) : 
            response()->json(['message' => 'No review found'], 404);
    }
}
