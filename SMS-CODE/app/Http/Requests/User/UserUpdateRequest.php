<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $user = $this->route('user');
        return [
            "name" => ['sometimes', 'string', 'max:30', 'regex:/^[A-Za-zА-Яа-яЁё\s]+$/u'],
            'phone' => [
                'sometimes',
                'string',
                'regex:/^\+?[0-9]+$/',
                'unique:users,phone,' . $user->id,
            ],
            'role' => ['sometimes', Rule::in([1, 2])]
        ];
    }
}
