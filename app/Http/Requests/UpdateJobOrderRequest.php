<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class UpdateJobOrderRequest extends FormRequest
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
            'date' => 'required|date|after_or_equal:today',
            'jos_date' => 'required|date|after_or_equal:date',
            'type_of_work_id' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hashids::decode($value)) {
                    $fail('Invalid Type of Work ID.');
                }
            }],
            'contractor_id' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hashids::decode($value)) {
                    $fail('Invalid Contractor ID.');
                }
            }],
            'conductor_id' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hashids::decode($value)) {
                    $fail('Invalid Conductor ID.');
                }
            }],
            'actual_work_completed' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:1000',
        ];
    }
}