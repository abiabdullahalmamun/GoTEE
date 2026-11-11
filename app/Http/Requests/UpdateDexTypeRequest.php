<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDexTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $rules = $this->rules();
        $fields = array_keys($rules);

        $this->merge([
            'updated_by' => auth()->user()->id,
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }

    public function rules()
    {
        return [
              'recId' => 'required|numeric',
           'name' => 'required|string|max:255',
            'status' => 'nullable|string',
            'created_by' => 'nullable|numeric',
            'created_at' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name field must be a string.',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status field must be a boolean.',
            'created_by.required' => 'The created_by field is required.',
            'created_by.numeric' => 'The created_by field must be a numeric.',
        ];
    }
}
