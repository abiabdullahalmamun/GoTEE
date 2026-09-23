<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAptOverrideRequest  extends FormRequest
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
            'centerId' => 'required|numeric', 
            'visatypeId' => 'required|numeric', 
            'formtype' => 'required|numeric', 
            'remarks' => 'required|string', 
            'WebFile_no' => [
                'nullable',
                'string',
                'regex:/^BGD.{9}$/'
            ],
            'phone' => [
            'nullable',
            'regex:/^0[0-9]{10}$/'
            ],
            'created_by' => 'nullable|numeric',
            'date' => 'required|date',
             'import_file' => 'nullable|file|mimes:xlsx,xls,csv|max:2048',
        ];
    }

  
    public function messages()
    {
        return [
             'formtype.required' => 'The formtype is required.',
            'centerId.required' => 'The centerId is required.',
             'visatypeId.required' => 'The sticker is required.',
          'remarks.required' => 'The remarks is required.',
            'date.required' => 'The date is required.',
            'WebFile_no.regex' => 'The WebFile_no must start with BGD and be exactly 12 characters.',
        ];
    }


}
