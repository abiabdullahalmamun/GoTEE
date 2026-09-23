<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRateRequest  extends FormRequest
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
            'nameD' => 'required|string', 
            'rate' => 'required|numeric', 
            'created_by'  =>'required|numeric', 
         
        ];
    }

    public function messages()
    {
        return [
            'nameD.required' => 'The nameD is required.',
            'rate.required' => 'The rate field must be a number.',
         ];
    }


}
