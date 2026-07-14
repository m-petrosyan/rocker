<?php

namespace App\Http\Requests\Event;

use App\Enums\EventTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class EventUpdateRequest extends EventCreateRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();
        $isAdmin = $user && in_array($user->role, ['admin', 'moderator'], true);

        if ($isAdmin) {
            return [
                'poster_file' => ['nullable', 'image', 'mimes:jpeg,jpg,webp,png', 'max:4000'],
                'country' => ['nullable', Rule::in(['am', 'ge'])],
                'title' => ['nullable', 'string', 'min:3', 'max:55'],
                'type' => ['nullable', Rule::in(EventTypeEnum::getValues())],
                'genre' => ['nullable', Rule::in(['rock', 'metal', 'all'])],
                'location' => ['nullable', 'string', 'min:5', 'max:255'],
                'cordinates' => ['nullable', 'array'],
                'cordinates.*' => ['required', 'numeric'],
                'bands' => ['nullable', 'array'],
                'bands.*.name' => ['nullable', 'string', 'max:255'],
                'bands.*.id' => ['nullable', 'integer', 'exists:bands,id'],
                'start_date' => ['nullable', 'date'],
                'end_date' => ['nullable', 'date'],
                'start_time' => ['nullable', 'date_format:H:i'],
                'content' => ['nullable', 'string', 'min:10', 'max:750'],
                'price' => ['nullable', 'string', 'max:20'],
                'link' => ['nullable', 'url', 'max:1000'],
                'ticket' => ['nullable', 'url', 'max:1000'],
            ];
        }

        return array_merge(
            parent::rules(),
            [
                'poster_file' => ['nullable', 'image', 'mimes:jpeg,jpg,webp,png', 'max:4000'],
            ]
        );
    }
}
