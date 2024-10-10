<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    /**
     * @OA\Info(
     *     title="Gestion des Transactions API",
     *     version="1.0.0",
     *     description="Une API pour gérer les transactions financières.",
     *     @OA\Contact(
     *         name="Support",
     *         email="support@example.com"
     *     ),
     *     @OA\License(
     *         name="MIT",
     *         url="https://opensource.org/licenses/MIT"
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Get all users",
     *     description="Retrieve a list of all users.",
     *     @OA\Response(
     *         response=200,
     *         description="A list of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function index()
    {
        $users = User::all();
        return $users;
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Users"},
     *     summary="Register a new user",
     *     description="Create a new user and return an API token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User Created Successfully"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOi..."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={
     *                 "email": {"The email has already been taken."}
     *             })
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
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
            'errors' => $validator->errors(),
        ], 422);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    $token = $user->createToken("API TOKEN")->plainTextToken;

    return response()->json([
        'message' => 'User Created Successfully',
        'token' => $token,
    ], 201);
}

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Users"},
     *     summary="Login a user",
     *     description="Authenticate a user and return an API token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOi...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Users"},
     *     summary="Logout a user",
     *     description="Revoke all tokens of the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not authenticated")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Get a specific user",
     *     description="Retrieve details of a specific user by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A specific user",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return $user;
    }

    public function connectedUser()
    {
        $user = Auth::user();
        return $user;
    }

    /**
 * @OA\Get(
 *     path="/isadmin",
 *     tags={"Users"},
 *     summary="Check if the authenticated user is an admin",
 *     description="Determine if the currently authenticated user has admin privileges. Returns true or false.",
 *     @OA\Response(
 *         response=200,
 *         description="Admin status of the authenticated user",
 *         @OA\JsonContent(
 *             type="boolean",
 *             example=true
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not authenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not authenticated")
 *         )
 *     ),
 *     @OA\Response(response=500, description="Internal server error")
 * )
 */
    public function isAdmin()
    {
        $user = Auth::user();
        if ($user->admin === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     description="Update user details.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","admin"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="admin", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     description="Remove a user from the system by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             type="string",
     *             example="User deleted successfully"
     *         )
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->transactions()->delete();
        $user->delete();
        return 'User deleted successfully';
    }
}
