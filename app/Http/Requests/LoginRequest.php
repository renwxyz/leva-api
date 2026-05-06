<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Email dinormalisasi agar pengecekan konsisten.
        // Password tidak diubah agar nilainya tetap sama seperti input pengguna.
        $this->merge([
            'email' => is_string($this->email) ? strtolower(trim($this->email)) : $this->email,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['bail', 'required', 'string', 'email', 'max:255'],
            'password' => ['bail', 'required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'email',
            'password' => 'password',
        ];
    }
}
