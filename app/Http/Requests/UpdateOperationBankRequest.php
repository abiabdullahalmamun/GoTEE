<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperationBankRequest extends FormRequest
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
            'catA' => 'required|numeric', 
        
            'codeB' => 'required|numeric', 
            'name' => 'required|string', 
             'defopn' =>  'required|in:0,1',
            'status' =>  'required|in:0,1',
            // 'created_by'  =>'required|numeric', 

        ];
    }

    public function messages()
    {
        return [
              'catA.required' => 'The category field must be a number.',
            'codeB.required' => 'The code field is required.',
             'name.required' => 'The name field is required.',
            'status.required' => 'The status field is required.',
   
        ];
    }
}
