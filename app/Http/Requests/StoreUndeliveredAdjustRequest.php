<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUndeliveredAdjustRequest  extends FormRequest
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
            'centerId' => 'required|numeric', 
            'import_file' => 'nullable|file|mimes:xlsx,xls,csv|max:2048',
             // 'import_file' => 'required|file|mimes:txt|max:20480',
            'created_by' => 'nullable|numeric',
        ];
    }

  
    public function messages()
    {
        return [
            'centerId.required' => 'The regionId is required.',
            'import_file.required' => 'The import file is required.',
            'import_file.mimes' => 'Only .txt files are allowed.',
            'import_file.max' => 'The import file can not be greater than 20MB.',
        ];
    }


}
