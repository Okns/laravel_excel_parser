<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        $input = $request->only(['name', 'password', 'email']);
        User::create([
            'name' => $input['name'],
            'password' => $input['password'],
            'email' => $input['email']
        ]);
        return response()->json(['success' => true, 'message' => 'User created']);

    }

    public function update(CreateUserRequest $request, $id)
    {
        $input = $request->only(['name', 'password', 'email']);
        $user = User::whereId($id)->first();
        $user->update([
            'name' => $input['name'],
            'password' => $input['password'],
            'email' => $input['email']
        ]);
        return response()->json(['success' => true, 'message' => 'User Created']);

    }
}
