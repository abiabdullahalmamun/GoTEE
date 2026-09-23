<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisaTypeRequest extends FormRequest
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
            
            'visatypeId' => 'required|numeric', 
            'visa_type' => 'required|string', 
            'symbol' => 'required|string', 
            'days' => 'required|numeric', 
            'status' =>  'required|in:0,1',
        
        ];
    }

    public function messages()
    {
        return [
           'visatypeId.required' => 'The visatypeId is required.',
            'visa_type.required' => 'The visa_type field must be a number.',
            'symbol.required' => 'The symbol field is required.',
            'days.required' => 'The days field is required.',
        ];
    }
}
