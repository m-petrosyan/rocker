<?php

namespace App\Http\Requests\User;

use App\Enums\CityEnum;
use App\Enums\CountryEnum;
use App\Enums\EventGenreEnum;
use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array|string>
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

            'password' => ['nullable', Password::min('8'), 'max:50'],
            're_password' => ['sometimes', 'required_with:password', 'same:password'],
            'info' => ['nullable', 'string', 'max:500'],
            'links' => ['nullable', 'array'],
            'links.*.url' => ['required', 'url'],
            'country' => ['nullable', Rule::in(CountryEnum::getValues())],
            'city' => ['nullable', Rule::in(CityEnum::getValues())],
            'genre' => ['nullable', Rule::in(EventGenreEnum::getValues())],
            'events_notifications' => ['nullable', 'boolean'],
        ];
    }
}
