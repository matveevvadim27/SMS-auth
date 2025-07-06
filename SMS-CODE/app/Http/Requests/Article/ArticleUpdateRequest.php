<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ArticleUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $user = $this->route('user');
        return [
            'name' => ['sometimes', 'string', 'max:50', 'regex:/^[A-Za-zА-Яа-яЁё\s]+$/u'],
            'phone' => [
                'sometimes',
                'string',
                'regex:/^\+?[0-9]+$/',
                'unique:users,phone,' . $user->id,
            ],
            'visibility' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
        ];
    }
}
