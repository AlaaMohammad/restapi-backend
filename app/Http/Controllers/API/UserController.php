<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        if ($user->save()) {
            return response()->json([
                'message' => 'Successfully Registered',
                'access_token' => $user->createToken('personal Access Token')->plainTextToken,
            ], 201);
        } else {
            return response()->json([
                'error' => 'There was an error'
            ]);
        }
        //return 'user.register';
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]
        );

        if (!Auth::attempt($credentials)) {
            // Authentication passed...
            return response()->json([
                'message' => 'Authentication failed'
            ], 401);
        }
        $user = Auth::user();
        return response()->json([
            'message' => 'Logged in successfully',
            'access_token' => $user->createToken('personal Access Token')->plainTextToken,
        ], 200);
        //return user.login';
    }
    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message'=>'Logged out successfully']);
    }

}
