<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPatch = $this->isMethod('PATCH') || $this->isMethod('PUT');
        $routeUser = $this->route('user');
        $userId = is_object($routeUser) ? $routeUser->id : $routeUser;

        $emailRules = [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($userId),
        ];

        if ($isPatch) {
            array_unshift($emailRules, 'sometimes');
        }

        return [
            'name' => $isPatch ? 'sometimes|required|string|max:255' : 'required|string|max:255',
            'email' => $emailRules,
            'password' => 'sometimes|nullable|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi',
            'email.required' => 'Email pengguna wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email pengguna sudah terdaftar',
            'password.min' => 'Password minimal 6 karakter',
        ];
    }
}
