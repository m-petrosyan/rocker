<?php

namespace App\Http\Requests\Band;

use Illuminate\Support\Str;

class BandUpdateRequest extends BandCreateRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Normalize name from array to string before validation
        if (is_array($this->name)) {
            $this->merge(['name' => $this->name['name'] ?? null]);
        }

        return array_merge(parent::rules(), [
            'name' => [
                'required', 'string', 'max:255',
                'unique:bands,name,'.$this->route('band')->id,
                function ($attribute, $value, $fail) {
                    $original = $this->route('band')->name;
                    $oldSlug = Str::slug($original);
                    $newSlug = Str::slug($value);

                    // 1. Check for minor typos
                    if (levenshtein($oldSlug, $newSlug) <= 3) {
                        return;
                    }

                    // 2. Check for subset/parts (e.g., removing translation or prefix)
                    // Split original and new by common separators
                    $separators = '/[-\/|]/u';
                    $originalParts = preg_split($separators, $original);
                    $newParts = preg_split($separators, $value);

                    // Check if new name matches any part of the original
                    foreach ($originalParts as $part) {
                        if (trim(mb_strtolower($part)) === trim(mb_strtolower($value))) {
                            return;
                        }
                    }

                    // Check if original name matches any part of the new one
                    foreach ($newParts as $part) {
                        if (trim(mb_strtolower($part)) === trim(mb_strtolower($original))) {
                            return;
                        }
                    }

                    $fail('The band name is too different from the current one. Only minor corrections or choosing a part of the original name are allowed.');
                },
            ],
            'cover_file' => ['nullable', 'image', 'mimes:jpeg,jpg,webp,png', 'max:15000'],
            'logo_file' => ['nullable', 'mimes:jpeg,jpg,webp,png,svg', 'max:4000'],
        ]);
    }
}
