<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperatorRequest extends FormRequest
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

        // $this->merge([
        //     'updated_by' => auth()->user()->id,
        //     'updated_at' => \Carbon\Carbon::now(),
        // ]);
    }

    public function rules()
    {
        return [
             'emp_id' => 'required|string|max:255',
            'emp_name' => 'required|string',
            'card_id' => 'required|string', 

            'status' =>  'required|in:0,1', // 'required|string',
            'pin' => 'required|digits:4',
            'id' => 'required|integer|min:1',
            // 'created_by' => 'required|numeric',
            // 'created_at' => 'required|date',

        ];
    }

    public function messages()
    {
        return [
            'emp_id.required' => 'The OperatorId is required.',
            'emp_id.string' => 'The OperatorId field must be a string.',
            'emp_name.required' => 'The name field is required.',
            'emp_name.string' => 'The name field must be a string.',
            'card_id.required' => 'The card_id field is required.',
             'card_id.string' => 'The card_id field must be a string.',
        
          
             'pin.digits' => 'The PIN must be exactly 4 digits.',
            'status.in' => 'The status must be either Active or Inactive.',
            'status.required' => 'The status field is required.',
            // 'created_by.required' => 'The created_by field is required.',
            // 'created_by.numeric' => 'The created_by field must be a numeric.',
        ];
    }
}
