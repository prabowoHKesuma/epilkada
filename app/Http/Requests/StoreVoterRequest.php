<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVoterRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:120'],
            'nik' => ['required', 'digits:16'],
            'kk' => ['required', 'digits:16'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'region_id' => ['required', 'exists:regions,id'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus terdiri dari tepat 16 digit angka.',
            'kk.digits' => 'Nomor KK harus terdiri dari tepat 16 digit angka.',
        ];
    }
}
