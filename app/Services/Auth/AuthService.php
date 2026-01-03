<?php

namespace App\Services\Auth;

use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function register(RegisterUserDTO $dto): string
    {
        $user = $this->userRepository->create([
            'name'     => $dto->name,
            'email'    => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        return $user->createToken('api-token')->accessToken;
    }

    public function login(LoginUserDTO $dto): string
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        return $user->createToken('api-token')->accessToken;
    }
}


