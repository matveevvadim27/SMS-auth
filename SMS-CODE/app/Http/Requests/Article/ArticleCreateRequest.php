<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class ArticleCreateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'regex:/^[A-Za-zА-Яа-яЁё\s]+$/u'],
            'phone' => [
                'required',
                'string',
                'regex:/^\+?[0-9]+$/',
                'unique:users,phone,'
            ],
            'visibility' => ['required', 'string'],
            'status' => ['required', 'string'],
            'QR_code' => ['nullable', 'url'],
        ];
    }
}
