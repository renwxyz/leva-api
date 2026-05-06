<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class OnboardingService
{
    public function complete(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Memastikan proses onboarding hanya berjalan untuk user yang valid.
            if (!$user->exists) {
                throw new InvalidArgumentException('Authenticated user is required.');
            }

            // Membatasi data onboarding ke field yang memang diperlukan oleh service.
            $safeData = $this->extractOnboardingData($data);

            $profile = $this->createProfile($user, $safeData);

            // future: VectorService
            // $this->vectorService->storeProfile($profile);

            $this->activateUser($user);

            return $user->refresh();
        });
    }

    private function createProfile(User $user, array $data): UserProfile
    {
        if ($user->profile) {
            throw new ConflictHttpException('User already completed onboarding.');
        }

        // Membatasi kolom yang ditulis ke database agar hanya data profil yang relevan yang tersimpan.
        return UserProfile::create([
            'user_id' => $user->id,
            'major' => $data['major'],
            'semester' => $data['semester'],
            'language_preference' => $data['language_preference'],
            'learning_style' => $data['learning_style'],
        ]);
    }

    private function activateUser(User $user): void
    {
        // Menghindari update berulang jika status user sudah aktif.
        if ($user->isActive()) {
            return;
        }

        $user->update([
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    private function extractOnboardingData(array $data): array
    {
        return [
            'major' => $data['major'],
            'semester' => $data['semester'],
            'language_preference' => $data['language_preference'],
            'learning_style' => $data['learning_style'],
        ];
    }
}
