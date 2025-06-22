<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractorRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:contractors|min:2|max:20',
            'email' => 'required|email|unique:contractors,email',
            'phone_number' => 'required|string|max:20|unique:contractors,phone_number',
            'company_name' => 'required|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
