<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesTypeRequest  extends FormRequest
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
        // $rules = $this->rules();
        // $fields = array_keys($rules);

        $this->merge([
            'created_by' => auth()->user()->id,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
          
            'status' =>  'required|in:0,1', // 'required|string',
        
            'created_by' => 'required|numeric',
            'created_at' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The OperatorId is required.',
            'name.string' => 'The OperatorId field must be a string.',
            
        
               'status.in' => 'The status must be either Active or Inactive.',
            'status.required' => 'The status field is required.',
       
            'created_by.required' => 'The created_by field is required.',
            'created_by.numeric' => 'The created_by field must be a numeric.',
        ];
    }


}
