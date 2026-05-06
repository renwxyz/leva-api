<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        // Controller hanya meneruskan data yang sudah tervalidasi ke service.
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        // Payload login dibatasi ke hasil validasi sebelum diteruskan ke service.
        $result = $this->authService->login($request->validated());

        return response()->json($result);
    }

    public function me(Request $request): JsonResponse
    {
        // Response hanya memuat data user yang diperlukan oleh client.
        $user = $request->user()->load('profile');

        return response()->json([
            'message' => 'Current user retrieved successfully',
            'data' => [
                'user' => $this->formatUserDetail($user),
            ],
        ]);
    }

    private function formatUserDetail($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'profile' => $user->profile ? [
                'major' => $user->profile->major,
                'semester' => $user->profile->semester,
                'language_preference' => $user->profile->language_preference,
                'learning_style' => $user->profile->learning_style,
            ] : null,
        ];
    }
}
