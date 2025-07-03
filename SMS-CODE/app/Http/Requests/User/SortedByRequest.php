<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SortedByRequest extends FormRequest
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
            'sort_by' => 'nullable|string|in:name,QR_quantity,phone,role,id,created_at,deleted_at',
            'sort_direction' => 'nullable|string|in:asc,desc'
        ];
    }
}
