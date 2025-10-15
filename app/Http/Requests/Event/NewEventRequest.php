<?php

namespace App\Http\Requests\Event;

use App\Enums\EventStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewEventRequest extends FormRequest
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
            'status' => ['required', Rule::in(EventStatusEnum::getValues())],
            'reason' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000'],
        ];
    }
}
