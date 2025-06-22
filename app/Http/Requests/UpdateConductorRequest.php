<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class UpdateConductorRequest extends FormRequest
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
        $hashedId = $this->route('hashedId');
        $id = Hashids::decode($hashedId)[0] ?? null;

        if (!$id) {
            abort(404, 'Invalid Conductor ID');

        }

        return [
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'staff_id' => "required|string|max:100|unique:conductors,staff_id,{$id}",
            'phone_number' => "required|string|max:20|unique:conductors,phone_number,{$id}",
            'department_name' => 'required|string|max:150',

            // Only validate password if provided
            // 'password' => 'nullable|confirmed|min:6',
        ];
    }
}
