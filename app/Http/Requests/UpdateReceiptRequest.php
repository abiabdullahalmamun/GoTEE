<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceiptRequest extends FormRequest
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
             'recId' => 'required|numeric', 
            'BookNo' => 'required|numeric', 
            'startNo' => 'required|numeric', 
            'endNo' => 'required|numeric', 
            'status' =>  'required|in:0,1',
            // 'created_by'  =>'required|numeric', 

        ];
    }

    public function messages()
    {
        return [
            'recId.required' => 'The recId is required.',
            'BookNo.required' => 'The BookNo is required.',
             'startNo.required' => 'The startNo is required.',
              'endNo.required' => 'The endNo is required.',
              'status.required' => 'The status field is required.',
       
        ];
    }
}
