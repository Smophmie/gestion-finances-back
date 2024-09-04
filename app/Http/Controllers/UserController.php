<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return $users;
    }



    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
        'password_confirmation' => 'required_with:password|same:password',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 401);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    $token = $user->createToken("API TOKEN")->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'User Created Successfully',
        'token' => $token,
    ], 200);
}


    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 401);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $user->tokens()->delete(); // Supprimer les anciens tokens
        $token = $user->createToken("API TOKEN")->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }


    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logout successful',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not authenticated',
        ], 401);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        return $user;
    }


    public function isAdmin(string $id)
    {
        $user = User::find($id);
        if ($user->admin === 1) {
            return response()->json(['is_admin' => true], 200);
        } else {
            return response()->json(['is_admin' => false], 200);
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'admin' => 'boolean|required',
        ]);
        $user = User::find($id);
        $user->update($request->all());
        return $user;
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->transactions()->delete();
        $user->delete();
        return 'User deleted successfully';
    }
}
