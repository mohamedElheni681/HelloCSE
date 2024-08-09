<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Services\AdminService;
use App\Http\Requests\RegisterAdminRequest;
use App\Http\Requests\LoginAdminRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;
use App\Domain\Entities\Admin;


class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @OA\Post(
     *     path="/api/admin/register",
     *     tags={"Admin"},
     *     summary="Register a new admin",
     *     description="Register a new admin",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Admin User"),
     *             @OA\Property(property="email", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Admin registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Admin registered successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function register(RegisterAdminRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $admin = $this->adminService->register($data);
        \Log::info('Admin registered with hashed password:', ['hashed_password' => $data['password']]);
        return response()->json(['message' => 'Admin registered successfully'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     tags={"Admin"},
     *     summary="Login an admin",
     *     description="Login an admin and get the authentication token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(LoginAdminRequest $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('admin-token')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }
    

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
