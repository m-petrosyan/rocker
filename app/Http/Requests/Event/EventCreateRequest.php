<?php

namespace App\Http\Requests\Event;

use App\Enums\CountyEnum;
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
            'title' => ['required', 'string', 'min:3', 'max:55'],
            'content' => ['required', 'string', 'min:10', 'max:730'],
            'type' => ['required', Rule::in([1, 2, 3])],
            'country' => ['required', Rule::in(['am'])],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'cordinates' => ['nullable', 'array'],
            'cordinates.*' => ['required', 'numeric'],
            'genre' => ['required', Rule::in(['rock', 'metal', 'all'])],
            'price' => ['nullable', 'string', 'max:30'],
            'link' => ['nullable', 'url', 'max:255'],
            'ticket' => ['nullable', 'url', 'max:255'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required'],
        ];
    }
}
