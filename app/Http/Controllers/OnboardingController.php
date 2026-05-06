<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingRequest;
use App\Services\OnboardingService;
use Illuminate\Http\JsonResponse;

class OnboardingController
{
    public function __construct(
        protected OnboardingService $onboardingService
    ) {}

    public function store(OnboardingRequest $request): JsonResponse
    {
        $user = $request->user();

        // Payload onboarding dibatasi ke data yang sudah lolos validasi sebelum diproses service.
        $updatedUser = $this->onboardingService->complete(
            $user,
            $request->validated()
        );

        // Response disusun ulang agar client hanya menerima data profil yang dibutuhkan.
        return response()->json([
            'message' => 'Onboarding completed successfully',
            'data' => [
                'user' => $this->formatUserDetail(
                    $updatedUser->load('profile')
                ),
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
