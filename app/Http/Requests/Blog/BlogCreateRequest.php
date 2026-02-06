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
            'images' => ['nullable', 'array', 'max:15'],
            'images.*' => ['image', 'mimes:webp,jpeg,png,jpg', 'max:50000'],
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
            $isContentEmpty = function ($value) {
                $cleaned = preg_replace('/\s+/u', '', strip_tags($value ?? ''));

                return empty($cleaned);
            };

            $hasPdfFile = !empty($this->file('pdf_file')) || !empty($this->file('pdf')) ||
                !empty($this->input('pdf_file')) || !empty($this->input('pdf'));

            $hasImages = !empty($this->file('images')) && is_array($this->file('images')) && count(
                    $this->file('images')
                ) > 0;

            if (!$hasPdfFile) {
                // English validation
                if (!empty($this->input('title.en')) || !empty($this->input('description.en')) || !$isContentEmpty(
                        $this->input('content.en')
                    )) {
                    if (empty($this->input('title.en'))) {
                        $validator->errors()->add(
                            'title.en',
                            'English title is required when any English field is provided.'
                        );
                    }
                    if (empty($this->input('description.en'))) {
                        $validator->errors()->add(
                            'description.en',
                            'English description is required when any English field is provided.'
                        );
                    }

                    // Content is required only if no images
                    if (!$hasImages) {
                        $contentEn = $this->input('content.en');
                        if ($isContentEmpty($contentEn)) {
                            $validator->errors()->add(
                                'content.en',
                                'English content is required when any English field is provided.'
                            );
                        } elseif (strlen(trim(strip_tags($contentEn))) < 50) {
                            $validator->errors()->add(
                                'content.en',
                                'English content must contain at least 50 characters excluding HTML tags.'
                            );
                        }
                    }
                }

                // Armenian validation
                if (!empty($this->input('title.am')) || !empty($this->input('description.am')) || !$isContentEmpty(
                        $this->input('content.am')
                    )) {
                    if (empty($this->input('title.am'))) {
                        $validator->errors()->add(
                            'title.am',
                            'Armenian title is required when any Armenian field is provided.'
                        );
                    }
                    if (empty($this->input('description.am'))) {
                        $validator->errors()->add(
                            'description.am',
                            'Armenian description is required when any Armenian field is provided.'
                        );
                    }

                    // Content is required only if no images
                    if (!$hasImages) {
                        $contentAm = $this->input('content.am');
                        if ($isContentEmpty($contentAm)) {
                            $validator->errors()->add(
                                'content.am',
                                'Armenian content is required and cannot consist only of HTML tags (e.g., <p><br></p> or <p></p>) when any Armenian field is provided.'
                            );
                        } elseif (strlen(trim(strip_tags($contentAm))) < 50) {
                            $validator->errors()->add(
                                'content.am',
                                'Armenian content must contain at least 50 characters excluding HTML tags.'
                            );
                        }
                    }
                }

                // Russian validation
                if (!empty($this->input('title.ru')) || !empty($this->input('description.ru')) || !$isContentEmpty(
                        $this->input('content.ru')
                    )) {
                    if (empty($this->input('title.ru'))) {
                        $validator->errors()->add(
                            'title.ru',
                            'Russian title is required when any Russian field is provided.'
                        );
                    }
                    if (empty($this->input('description.ru'))) {
                        $validator->errors()->add(
                            'description.ru',
                            'Russian description is required when any Russian field is provided.'
                        );
                    }

                    // Content is required only if no images
                    if (!$hasImages) {
                        $contentRu = $this->input('content.ru');
                        if ($isContentEmpty($contentRu)) {
                            $validator->errors()->add(
                                'content.ru',
                                'Russian content is required when any Russian field is provided.'
                            );
                        } elseif (strlen(trim(strip_tags($contentRu))) < 50) {
                            $validator->errors()->add(
                                'content.ru',
                                'Russian content must contain at least 50 characters excluding HTML tags.'
                            );
                        }
                    }
                }

                $allEmpty = (empty($this->input('title.en')) && empty(
                        $this->input(
                            'description.en'
                        )
                        ) && $isContentEmpty($this->input('content.en'))) &&
                    (empty($this->input('title.am')) && empty($this->input('description.am')) && $isContentEmpty(
                            $this->input('content.am')
                        )) &&
                    (empty($this->input('title.ru')) && empty($this->input('description.ru')) && $isContentEmpty(
                            $this->input('content.ru')
                        ));

                if ($allEmpty) {
                    $validator->errors()->add(
                        'title',
                        'At least one language (English, Armenian or Russian) must have title, description, and content provided.'
                    );
                }
            }
        });
    }
}