<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $token = $this->authService->register(
            new RegisterUserDTO(
                $validated['name'],
                $validated['email'],
                $validated['password']
            )
        );

        return response()->json(['token' => $token], 201);
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $token = $this->authService->login(
            new LoginUserDTO(
                $validated['email'],
                $validated['password']
            )
        );

        return response()->json(['token' => $token]);
    }
}
