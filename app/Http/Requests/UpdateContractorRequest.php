<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class UpdateContractorRequest extends FormRequest
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
            abort(404, 'Invalid Contractor ID');
        }

        return [
            'name' => 'required|string|max:255',
            'code' => "required|string|min:2|max:20|unique:contractors,code,{$id}",
            'email' => "required|email|unique:contractors,email,{$id}",
            'phone_number' => "required|string|max:20|unique:contractors,phone_number,{$id}",
            'company_name' => 'required|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            // 'password' => 'nullable|string|min:6|confirmed',
        ];
    }
}
