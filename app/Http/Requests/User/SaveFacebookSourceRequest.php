<?php

namespace App\Http\Requests\User;

use App\Models\UserFacebookPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveFacebookSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) auth()->user();
    }

    public function rules(): array
    {
        $user = auth()->user();

        return [
            'fb_page_url' => [
                'required',
                'string',
                'max:500',
                'regex:/facebook\.com|fb\.com/i',
                Rule::unique('user_facebook_pages', 'page_url'),
                function ($attribute, $value, $fail) use ($user) {
                    $count = UserFacebookPage::where('user_id', $user->id)->count();
                    if ($count >= 3) {
                        $fail('Maximum 3 Facebook pages can be connected.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fb_page_url.required' => 'Enter the Facebook page URL',
            'fb_page_url.unique' => 'This Facebook page is already connected by another user.',
        ];
    }
}
