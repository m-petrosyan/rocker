<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'min:2', 'max:50'],
            'username' => [
                'nullable',
                'string',
                'min:2',
                'max:50',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ],
//            'email' => ['nullable', 'email', Rule::unique('users', 'email'), 'max:100'],
            'password' => ['nullable', Password::min('8'), 'max:50'],
            're_password' => ['sometimes', 'required_with:password', 'same:password'],
            'info' => ['nullable', 'string', 'max:500'],
            'links' => ['nullable', 'array'],
            'links.*.url' => ['required', 'url'],
        ];
    }
}
