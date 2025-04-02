<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2'],
            'username' => ['required', 'string', 'min:2', 'max:50', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email'), 'max:100'],
            'password' => ['required', Password::min('8')],
        ];
    }
}
