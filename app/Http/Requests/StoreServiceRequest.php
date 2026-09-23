<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
            'created_by' => auth()->user()->id,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }

    public function rules()
    {
        return [
            'svcName' => 'required|string|max:255',
            'type' => 'nullable|int',
            'status' => 'nullable|int',
             'defsec' => 'nullable|int',
          'created_by' => 'nullable|numeric',
            'created_at' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'svcName.required' => 'The name field is required.',
            'svcName.string' => 'The name field must be a string.',
            'type.required' => 'The type field is required.',
             'defsec.required' => 'The type field is required.',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status field must be a boolean.',
            'created_by.required' => 'The created_by field is required.',
            'created_by.numeric' => 'The created_by field must be a numeric.',
        ];
    }
}
