<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShopRequest extends FormRequest
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
            // 'name' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            // 'status' => 'nullable|string',
            // 'updated_by' => 'nullable|numeric',
            // 'updated_at' => 'nullable|date',

           'ShopName' => 'required|string|max:255',
            'Contact' => 'nullable|string',
            'message' => 'nullable|string',
            'status' => 'nullable|string',
            'created_by' => 'nullable|numeric',
            'created_at' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            // 'name.required' => 'The name field is required.',
            // 'name.string' => 'The name field must be a string.',
            // 'name.max' => 'The name field must not exceed 255 characters.',
            // 'description.required' => 'The description field is required.',
            // 'description.string' => 'The description field must be a string.',
            // 'status.required' => 'The status field is required.',
            // 'status.boolean' => 'The status field must be a boolean.',
            // 'updated_by.required' => 'The updated_by field is required.',
            // 'updated_by.numeric' => 'The updated_by field must be a numeric.',
            // 'updated_at.required' => 'The updated_at field is required.',
            // 'updated_at.string' => 'The updated_at field must be a string.',

                        'ShopName.required' => 'The name field is required.',
            'ShopName.string' => 'The name field must be a string.',
            'ShopName.max' => 'The name field must not exceed 255 characters.',
            'Contact.required' => 'The description field is required.',
            'Contact' => ['required', 'regex:/^0\d{9}$/'], 
            // 'Contact.string' => 'The description field must be a string.',
            'message.required' => 'The Message field is required.',
            'message.string' => 'The Message field must be a string.',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status field must be a boolean.',
            'created_by.required' => 'The created_by field is required.',
            'created_by.numeric' => 'The created_by field must be a numeric.',
        ];
    }
}
