<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'role' => ['required', Rule::in([1, 2])]
        ];
    }
}
