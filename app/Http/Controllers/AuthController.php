<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Authenticate the user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ]);

            $user = User::query()->where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'token' => $user->createToken('token', [])->plainTextToken,
                ],
                'message' => 'Token generated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Register a new user
     *
     * @param Request $request
     * @return JSONResponse
     */
    public function register(Request $request) : JSONResponse
    {
        try {

            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'data' => [
                    'token' => $user->createToken('token', [])->plainTextToken,
                ],
                'message' => 'User created successfully',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Logout the user (Revoke the token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request) : JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logged out successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
