<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormFillRequest extends FormRequest
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

    // public function rules(): array
    // {
    //     return [
    //         'PrintedSerial' => 'required|string|max:255',
    //         'tag' => 'required|string|max:255',
    //     ];
    // }

    public function rules()
    {
        return [
            'wf_no' => [
                    'required',
                    'string',
                    'regex:/^BGD.{9}$/'
                ],
        
            'name' => 'required|string',
            'passport1' => 'required|string',
           
            'contact' => [
                'required',
                'string',
                'regex:/^0.{10}$/'
            ],             
            'created_by' => 'required|numeric',
            'fee' => 'required|numeric',
             'remarks' => 'nullable|string',
         ];
    }

  
    public function messages()
    {
        return [
            'wf_no.regex' => 'The WebFile_no must start with BGD and be exactly 12 characters.',
            'contact.required' => 'Contact Number must be number starting with 0',
            'name.required' => 'The name is required.',
            'passport1.required' => 'The passport is required.',
           
        ];
    }


}
