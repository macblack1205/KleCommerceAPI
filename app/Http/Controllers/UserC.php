<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;

class UserC extends Controller
{

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $request->hasFile('photo') ?
            $data['photo'] = $request->file('photo')->store('public/photos') : 
            $data['photo'] = 'photos/default.jpg'; // Path to default image
        $user = User::create($data);
        return response()->json(['message'=>'Registration success. Welcome to our team!','user'=>$user], 201);
    }

    public function show($id)
    {
        $user = User::with(['addresses', 'coupons'])->find($id);
        return response()->json($user);
    }
    
    public function update(UserRequest $request, $id)
    {   
        $user = User::findOrFail($id);
        if ($request->user()->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }   
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            if ($user->photo && $user->photo !== 'photos/default.jpg') 
                Storage::delete($user->photo);
            $data['photo'] = $request->file('photo')->store('public/photos');
        }
        $user->update($data);
        return response()->json(['user' =>$user]);
    }

    public function destroy(Request $request, $id)
    {   $user = User::findOrFail($id);
        if ($request->user()->id !== $user->id) 
            return response()->json(['message' => 'Unauthorized'], 403);
        $user->delete();
        return response()->json(null, 204);
    }
}
