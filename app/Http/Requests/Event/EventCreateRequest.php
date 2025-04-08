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
            'poster_file' => ['nullable', 'image', 'mimes:jpeg,jpg,webp,png', 'max:4000'],
            'title' => ['required', 'string', 'min:3', 'max:55'],
            'content' => ['required', 'string', 'min:10', 'max:730'],
            'type' => ['required', Rule::in([1, 2, 3])],
            'country' => ['required', Rule::in(['am'])],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'cordinates' => ['nullable', 'array'],
            'cordinates.latitude' => ['required_with:cordinates', 'numeric'],
            'cordinates.longitude' => ['required_with:cordinates', 'numeric'],
            'genre' => ['required', Rule::in(['rock', 'metal', 'all'])],
            'price' => ['nullable', 'numeric', 'min:0'],
            'link' => ['nullable', 'url', 'max:10000'],
            'ticket' => ['nullable', 'url', 'max:10000'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
        ];
    }

//    public function messages(): array
//    {
//        return [
//            'poster_file.image' => 'The poster must be an image.',
//            'poster_file.mimes' => 'The poster must be a file of type: jpeg, jpg, webp, png.',
//            'poster_file.max' => 'The poster may not be greater than 4MB.',
//            'title.required' => 'The title is required.',
//            'title.min' => 'The title must be at least 3 characters.',
//            'title.max' => 'The title may not be greater than 55 characters.',
//            'content.required' => 'The content is required.',
//            'content.min' => 'The content must be at least 10 characters.',
//            'content.max' => 'The content may not be greater than 730 characters.',
//            'type.required' => 'The event type is required.',
//            'type.in' => 'The selected event type is invalid.',
//            'country.required' => 'The country is required.',
//            'country.in' => 'The selected country is invalid.',
//            'location.required' => 'The location is required.',
//            'location.min' => 'The location must be at least 5 characters.',
//            'location.max' => 'The location may not be greater than 255 characters.',
//            'cordinates.latitude.required_with' => 'The latitude is required when coordinates are provided.',
//            'cordinates.longitude.required_with' => 'The longitude is required when coordinates are provided.',
//            'genre.required' => 'The genre is required.',
//            'genre.in' => 'The selected genre is invalid.',
//            'price.numeric' => 'The price must be a number.',
//            'price.min' => 'The price cannot be negative.',
//            'link.url' => 'The link must be a valid URL.',
//            'ticket.url' => 'The ticket link must be a valid URL.',
//            'start_date.required' => 'The start date is required.',
//            'start_date.after_or_equal' => 'The start date must be today or later.',
//            'start_time.required' => 'The start time is required.',
//            'start_time.date_format' => 'The start time must be in HH:MM format (e.g., 13:45).',
//        ];
//    }
}
