<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnboardingRequest extends FormRequest
{
    private const ALLOWED_MAJORS = [
        'Teknik Informatika',
        'Sistem Informasi',
        'Sains Data',
        'Rekayasa Perangkat Lunak',
        'Teknik Elektro',
        'Teknik Mesin',
        'Ilmu Komunikasi',
        'Psikologi',
        'Hukum',
        'Kedokteran',
        'Manajemen',
        'Akuntansi',
        'Desain Komunikasi Visual',
        'Sastra Inggris',
        'Ilmu Politik',
        'Farmasi',
        'Arsitektur',
        'Teknik Sipil',
    ];

    private const ALLOWED_SEMESTERS = [1, 2, 3, 4, 5, 6, 7, 8];

    private const ALLOWED_LANGUAGES = ['Indonesian', 'English'];

    private const ALLOWED_LEARNING_STYLES = ['visual'];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Normalisasi hanya diterapkan pada field yang dapat berubah format saat dikirim dari FE.
        // `major` dirapikan sebagai input pencarian, sedangkan `semester` dikonversi ke integer.
        // Nilai pilihan lain divalidasi langsung melalui whitelist pada rules.
        $this->merge([
            'major' => is_string($this->major) ? trim($this->major) : $this->major,
            'semester' => is_numeric($this->semester) ? (int) $this->semester : $this->semester,
        ]);
    }

    public function rules(): array
    {
        return [
            'major' => ['bail', 'required', 'string', Rule::in(self::ALLOWED_MAJORS)],
            'semester' => ['bail', 'required', 'integer', Rule::in(self::ALLOWED_SEMESTERS)],
            'language_preference' => ['bail', 'required', 'string', Rule::in(self::ALLOWED_LANGUAGES)],
            'learning_style' => ['bail', 'required', 'string', Rule::in(self::ALLOWED_LEARNING_STYLES)],
        ];
    }
}
