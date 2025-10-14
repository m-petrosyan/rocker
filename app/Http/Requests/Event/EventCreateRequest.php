<?php

namespace App\Http\Requests\Event;

use App\Enums\GenreEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventCreateRequest extends FormRequest
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
            'poster_file' => ['required', 'image', 'mimes:jpeg,jpg,webp,png', 'max:4000'],
            'title' => ['required', 'string', 'min:3', 'max:55'],
            'content' => ['required', 'string', 'min:10', 'max:750'],
            'type' => ['required', Rule::in([1, 2, 3])],
            'country' => ['required', Rule::in(['am', 'ge'])],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'cordinates' => ['nullable', 'array'],
            'cordinates.*' => ['required', 'numeric'],
            'genre' => ['required', Rule::in(['rock', 'metal', 'all'])],
            'price' => ['nullable', 'string', 'max:15'],
            'link' => ['nullable', 'url', 'max:1000'],
            'ticket' => ['nullable', 'url', 'max:1000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'bands' => ['array'],
            'bands.*.name' => ['required', 'string', 'max:255'],
            'bands.*.id' => ['nullable', 'integer', 'exists:bands,id'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'content' => mb_convert_encoding($this->input('content'), 'UTF-8'),
        ]);
    }

    public function messages(): array
    {
        return [
            'poster_file.mimetypes' => 'Файл должен быть в формате JPEG, JPG, WEBP или PNG. GIF не поддерживается.',
        ];
    }
}
