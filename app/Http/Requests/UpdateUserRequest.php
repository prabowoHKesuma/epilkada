<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:8'], // opsional saat edit
            'organization_id' => ['required', 'exists:organizations,id'],
            'region_id' => ['required', 'exists:regions,id'],
            'role' => ['required', 'exists:roles,name'],
        ];
    }
}
