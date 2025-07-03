<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
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

        ];
    }
}
