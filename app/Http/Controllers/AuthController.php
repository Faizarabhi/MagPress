<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try{
            request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = Auth::login($user);
        return response()->json([
            'status' => 'Registration successful',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'Bearer',
            ],
        ])->setStatusCode(201, 'Created');}
        catch(\Throwable $e){
            return response()->json([
                'status' => 'Registration failed',
                'message' => $e->getMessage(),
            ])->setStatusCode(400, 'Bad Request');

        }


}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'Unauthorized',
            ])->setStatusCode(401, 'Unauthorized');
        }
        $user = Auth::user();
        return response()->json([
            'status' => 'Login successful',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'Bearer',
            ],
        ])->setStatusCode(200, 'OK');
    }
}
