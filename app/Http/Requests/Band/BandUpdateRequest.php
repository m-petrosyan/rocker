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
                    if (levenshtein(Str::slug($original), Str::slug($value)) > 3) {
                        $fail('The band name is too different from the current one. Only minor corrections (typos) are allowed.');
                    }
                },
            ],
            'cover_file' => ['nullable', 'image', 'mimes:jpeg,jpg,webp,png', 'max:15000'],
            'logo_file' => ['nullable', 'mimes:jpeg,jpg,webp,png,svg', 'max:4000'],
        ]);
    }
}
