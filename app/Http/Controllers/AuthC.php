<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthC extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'sometimes|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Please check the input data.',
            'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' =>'No user exists with this email'], 404);

        if (!$request->filled('password')) {
            return response()->json([
                'message' => 'User exists, please provide your password to login.',
                'status' => 'Password required'], 200);
        } 

        if ($request->filled('password')) {
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json(['message' => 'Invalid email or password'], 401);
            }
            return response()->json([
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
                'message' =>'Logged in successfully'
            ], 200);
        }        
    }

    public function logout(Request $request){
        $allDevices = $request->input('all', false) === 'true';
        $allDevices ? $request->user()->tokens()->delete() : $request->user()->currentAccessToken()->delete();        
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
