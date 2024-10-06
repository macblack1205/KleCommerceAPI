<?php

namespace App\Http\Controllers;


use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressC extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->get();
        return $addresses ? response()->json($addresses, 200) : response()->json(['message' =>'No addresses found'], 404);
    }

    public function store(AddressRequest $request)
    {
        $address = Address::create(
            array_merge($request->validated(), ['user_id' => Auth::id()])
        );
        return response()->json(['address'=>$address], 201);
    }

    public function show($id)
    {
        $address = Address::findOrFail($id);
        return $address ? response()->json($address, 200) : response()->json(['message' =>'No addresses found'], 404);
    }

    public function update(AddressRequest $request, $id)
    {
        $address = Address::findOrFail($id);
        if ($request->user()->id !== $address->user_id)
            return response()->json(['message' => 'Unauthorized'], 403);
        $address->update($request->validated());
        return response()->json($address);
    }

    public function destroy(Request $request, $id)
    {
        $address = Address::findOrFail($id);
        if ($request->user()->id !== $address->user_id)
            return response()->json(['message' => 'Unauthorized'], 403);
        $address->delete();
        return response()->json(null, 204);
    }
}
