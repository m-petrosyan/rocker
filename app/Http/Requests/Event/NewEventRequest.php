<?php

namespace App\Http\Requests\Event;

use App\Enums\EventStatusEnum;
use App\Models\Event;
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

    public function after(): array
    {
        return [
            function ($validator) {
                if ($this->input('status') !== EventStatusEnum::ACCEPTED->value) {
                    return;
                }

                /** @var Event $event */
                $event = $this->route('event');

                if (! $event) {
                    return;
                }

                $missing = [];

                if (! $event->getFirstMedia('poster')) {
                    $missing[] = 'poster';
                }
                if (empty($event->country)) {
                    $missing[] = 'country';
                }
                if (empty($event->title) || mb_strlen($event->title) < 3) {
                    $missing[] = 'title';
                }
                if (empty($event->type)) {
                    $missing[] = 'type';
                }
                if (empty($event->genre)) {
                    $missing[] = 'genre';
                }
                if (empty($event->location) || $event->location === '—' || mb_strlen($event->location) < 5) {
                    $missing[] = 'location';
                }
                if (empty($event->start_date)) {
                    $missing[] = 'start_date';
                }
                if (empty($event->start_time)) {
                    $missing[] = 'start_time';
                }
                if (empty($event->content) || mb_strlen($event->content) < 10) {
                    $missing[] = 'content';
                }

                if (! empty($missing)) {
                    $validator->errors()->add(
                        'status',
                        'Cannot accept event. Fill in required fields: '.implode(', ', $missing)
                    );
                }
            },
        ];
    }
}
