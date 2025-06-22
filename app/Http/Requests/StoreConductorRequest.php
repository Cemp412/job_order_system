<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConductorRequest extends FormRequest
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
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'staff_id' => 'required|string|max:100|unique:conductors,staff_id',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20|unique:conductors,phone_number',
            'department_name' => 'required|string|max:150',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
