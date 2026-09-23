<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortRequest extends FormRequest
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

    }

    public function rules()
    {
        return [
            'portId' => 'required|numeric', 
         
            'portName' => 'required|string', 
            'created_by' => 'nullable|numeric',
            'created_at' => 'nullable|date',
             'status' =>  'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
         
            'portName.required' => 'The counterName field is required.',
      
        ];
    }
}
