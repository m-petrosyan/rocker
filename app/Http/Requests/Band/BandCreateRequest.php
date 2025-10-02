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
            'cover_file' => ['required', 'image', 'mimes:webp,jpeg,jpg,webp,png', 'max:15000'],
            'logo_file' => ['required', 'mimes:jpeg,jpg,webp,png,svg', 'max:4000'],
            'images' => ['nullable', 'array', 'max:7'],
            'images.*' => ['image', 'mimes:webp,jpeg,png,jpg', 'max:25000'],
            'info' => ['required', 'string', 'min:100', 'max:4000'],
            'genres' => ['required', 'array', 'max:255'],
            'links' => ['nullable', 'array'],
            'links.*.url' => ['required', 'url'],
            'cover_position' => ['nullable', 'array'],
            'albums' => ['nullable', 'array'],
            'albums.*' => ['nullable', 'array'],
            'albums.*.id' => ['nullable', 'integer', 'exists:albums,id'],
            'albums.*.title' => ['required', 'string', 'max:255'],
            'albums.*.year' => ['required', 'digits:4'],
            'albums.*.tracks_count' => ['required', 'numeric', 'min:1', 'max:100'],
            'albums.*.cover_file' => ['nullable', 'image', 'mimes:jpeg,jpg,webp,png', 'max:15000'],
            'albums.*.links' => ['nullable', 'array'],
            'albums.*.links.*.url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            // Links
            'links.*.url.required' => 'Each URL must be provided.',
            'links.*.url.url' => 'Each URL must be a valid url address.',

            // Albums
            'albums.*.title.required' => 'Each album must include a title.',
            'albums.*.year.required' => 'Each album must include a release year.',
            'albums.*.year.digits' => 'The release year must be exactly 4 digits (e.g., 2024).',
            'albums.*.tracks_count.required' => 'Each album must specify the number of tracks.',
            'albums.*.tracks_count.numeric' => 'The number of tracks must be a number.',
            'albums.*.tracks_count.min' => 'An album must have at least :min track.',
            'albums.*.tracks_count.max' => 'An album cannot have more than :max tracks.',
        ];
    }
}
