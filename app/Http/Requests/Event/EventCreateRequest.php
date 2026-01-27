<?php

namespace App\Http\Requests\Event;

use App\Enums\EventTypeEnum;
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
            'country' => ['required', Rule::in(['am', 'ge'])],
            'title' => ['required', 'string', 'min:3', 'max:55'],
            'type' => ['required', Rule::in(EventTypeEnum::getValues())],
            'genre' => ['required', Rule::in(['rock', 'metal', 'all'])],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'cordinates' => ['nullable', 'array'],
            'cordinates.*' => ['required', 'numeric'],
            'bands' => ['array'],
            'bands.*.name' => ['required', 'string', 'max:255'],
            'bands.*.id' => ['nullable', 'integer', 'exists:bands,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'content' => ['required', 'string', 'min:10', 'max:750'],
            'price' => ['nullable', 'string', 'max:20'],
            'link' => ['nullable', 'url', 'max:1000'],
            'ticket' => ['nullable', 'url', 'max:1000'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'content' => mb_convert_encoding($this->input('content'), 'UTF-8'),
        ]);
    }
}
