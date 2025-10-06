<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BlogCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.en' => ['nullable', 'string', 'min:5'],
            'title.am' => ['nullable', 'string', 'min:5'],
            'title.ru' => ['nullable', 'string', 'min:5'],
            'description' => ['required', 'array'],
            'description.en' => ['nullable', 'string', 'min:20'],
            'description.am' => ['nullable', 'string', 'min:20'],
            'description.ru' => ['nullable', 'string', 'min:20'],
            'content' => ['required', 'array'],
            'content.en' => ['nullable', 'string'],
            'content.am' => ['nullable', 'string'],
            'content.ru' => ['nullable', 'string'],
            'cover_file' => ['required', 'image', 'mimes:jpeg,jpg,webp,png', 'max:15000'],
            'bands' => ['array'],
            'bands.*.name' => ['required', 'string', 'max:255'],
            'bands.*.id' => ['nullable', 'integer', 'exists:bands,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:10000'],
        ];
    }

    /**
     * Add custom validation logic after the initial rules.
     *
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $title = $this->input('title', []);
            $description = $this->input('description', []);
            $content = $this->input('content', []);

            $isContentEmpty = function ($value) {
                $cleaned = preg_replace('/\s+/u', '', strip_tags($value ?? ''));

                return empty($cleaned);
            };

            $hasPdfFile = !empty($this->file('pdf_file')) || !empty($this->file('pdf')) ||
                !empty($this->input('pdf_file')) || !empty($this->input('pdf'));

            if (!$hasPdfFile && (!empty($title['en']) || !empty($description['en']) || !$isContentEmpty(
                        $content['en']
                    ))) {
                if (empty($title['en'])) {
                    $validator->errors()->add(
                        'title.en',
                        'English title is required when any English field is provided.'
                    );
                }
                if (empty($description['en'])) {
                    $validator->errors()->add(
                        'description.en',
                        'English description is required when any English field is provided.'
                    );
                }
                if ($isContentEmpty($content['en'])) {
                    $validator->errors()->add(
                        'content.en',
                        'English content is required when any English field is provided.'
                    );
                } elseif (strlen(trim(strip_tags($content['en']))) < 50) {
                    $validator->errors()->add(
                        'content.en',
                        'English content must contain at least 50 characters excluding HTML tags.'
                    );
                }
            }

            // Проверка для армянского языка
            if (!$hasPdfFile && (!empty($title['am']) || !empty($description['am']) || !$isContentEmpty(
                        $content['am']
                    ))) {
                if (empty($title['am'])) {
                    $validator->errors()->add(
                        'title.am',
                        'Armenian title is required when any Armenian field is provided.'
                    );
                }
                if (empty($description['am'])) {
                    $validator->errors()->add(
                        'description.am',
                        'Armenian description is required when any Armenian field is provided.'
                    );
                }
                if ($isContentEmpty($content['am'])) {
                    $validator->errors()->add(
                        'content.am',
                        'Armenian content is required and cannot consist only of HTML tags (e.g., <p><br></p> or <p></p>) when any Armenian field is provided.'
                    );
                } elseif (strlen(trim(strip_tags($content['am']))) < 50) {
                    $validator->errors()->add(
                        'content.am',
                        'Armenian content must contain at least 50 characters excluding HTML tags.'
                    );
                }
            }

            // Проверка для русского языка
            if (!$hasPdfFile && (!empty($title['ru']) || !empty($description['ru']) || !$isContentEmpty(
                        $content['ru']
                    ))) {
                if (empty($title['ru'])) {
                    $validator->errors()->add(
                        'title.ru',
                        'Russian title is required when any Russian field is provided.'
                    );
                }
                if (empty($description['ru'])) {
                    $validator->errors()->add(
                        'description.ru',
                        'Russian description is required when any Russian field is provided.'
                    );
                }
                if ($isContentEmpty($content['ru'])) {
                    $validator->errors()->add(
                        'content.ru',
                        'Russian content is required when any Russian field is provided.'
                    );
                } elseif (strlen(trim(strip_tags($content['ru']))) < 50) {
                    $validator->errors()->add(
                        'content.ru',
                        'Russian content must contain at least 50 characters excluding HTML tags.'
                    );
                }
            }

            if ((empty($title['en']) && empty($description['en']) && $isContentEmpty($content['en'])) &&
                (empty($title['am']) && empty($description['am']) && $isContentEmpty($content['am'])) &&
                (empty($title['ru']) && empty($description['ru']) && $isContentEmpty($content['ru']))) {
                $validator->errors()->add(
                    'title',
                    'At least one language (English, Armenian or Russian) must have title, description, and content provided.'
                );
            }
        });
    }
}