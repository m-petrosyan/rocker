<?php

namespace App\Http\Requests\Band;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BandCreateRequest extends FormRequest
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
            'name' => ['required', 'array', 'max:255'],
            'cover_file' => ['nullable', 'image', 'mimes:jpeg,jpg,webp,png', 'max:15000'],
            'logo_file' => ['nullable', 'image', 'mimes:jpeg,jpg,webp,png,svg', 'max:4000'],
            'info' => ['required', 'string', 'min:10', 'max:730'],
            'genres' => ['required', 'array', 'max:255'],
        ];
    }
}
