<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisaTypeRequest  extends FormRequest
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
            'visa_type' => 'required|string', 
            'symbol' => 'required|string', 
            'days' => 'required|numeric', 
            'status' =>  'required|in:0,1',
            'created_by'  =>'required|numeric', 
        ];
    }

    public function messages()
    {
        return [
            'visa_type.required' => 'The visa_type is required.',
            'symbol.required' => 'The symbol required.',
            'days.required' => 'The days field is required.',
            
        ];
    }


}
