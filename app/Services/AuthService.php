<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Memastikan service hanya memproses field yang memang dibutuhkan.
            $safeData = $this->extractRegisterData($data);

            return User::create([
                'name' => $safeData['name'],
                'email' => $safeData['email'],
                'password' => $safeData['password'],
                'status' => User::STATUS_PENDING,
            ]);
        });
    }

    public function login(array $data): array
    {
        // Membatasi payload login ke field yang memang digunakan pada proses autentikasi.
        $safeData = $this->extractLoginData($data);

        $user = User::where('email', $safeData['email'])->first();

        if (!$user || !Hash::check($safeData['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        // Menghapus token lama agar token aktif tidak menumpuk.
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                ],
                'token' => $token,
            ],
        ];
    }

    private function extractRegisterData(array $data): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    private function extractLoginData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }
}
