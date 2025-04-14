<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GalleryCreateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'date' => ['nullable', 'date'],
            'images' => ['required', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:20480'],
            'cid' => ['nullable', 'string'],
            'location' => ['required_with:cid', 'nullable', 'string', 'max:255'],
            'cordinates' => ['required_with:cid', 'nullable', 'array'],
            'cordinates.*' => ['required', 'numeric'],
            'bands' => ['array'],
            'bands.*.name' => ['required', 'string', 'max:255'],
            'bands.*.id' => ['nullable', 'integer', 'exists:bands,id'],
        ];
    }
}
