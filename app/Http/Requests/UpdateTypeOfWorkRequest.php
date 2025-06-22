<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vinkla\Hashids\Facades\Hashids;

class UpdateTypeOfWorkRequest extends FormRequest
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
        $hashedId = $this->route('hashedId'); // not `typeOfWork`
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashedId)[0] ?? null;

        return [
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:10',
            'code' => "required|string|max:50|unique:type_of_works,code,$id",
        ];
    }
}
