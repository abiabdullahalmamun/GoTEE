<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceiptRequest  extends FormRequest
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
              'BookNo' => 'required|numeric', 
              'startNo' => 'required|numeric', 
              'endNo' => 'required|numeric', 
              'status' =>  'required|in:0,1',
            'created_by'  =>'required|numeric', 
           
        ];
    }

    public function messages()
    {
        return [
            'BookNo.required' => 'The BookNo is required.',
            'startNo.required' => 'The startNo is required.',
            'endNo.required' => 'The endNo is required.',
            'created_by.required' => 'The created_by field is required.',
            'status.required' => 'The status field is required.',
            
        ];
    }


}
