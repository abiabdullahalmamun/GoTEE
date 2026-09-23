<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
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
