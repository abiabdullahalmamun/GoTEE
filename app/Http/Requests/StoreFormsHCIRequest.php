<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormsHCIRequest  extends FormRequest
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
            'regionId' => 'required|numeric', 
            'remarks' => 'required|string', 
            'created_by' => 'nullable|numeric',
            'date' => 'required|date',
            'filetype' => 'required|numeric',
             'import_file' => 'nullable|file|mimes:xlsx,xls,csv|max:2048',
        ];
    }

  
    public function messages()
    {
        return [
            
            'regionId.required' => 'The centerId is required.',
            'remarks.required' => 'The remarks is required.',
            'date.required' => 'The date is required.',
             'filetype.required' => 'The filetype is required.',
           
        ];
    }


}
