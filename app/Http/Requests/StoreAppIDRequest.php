<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppIDRequest  extends FormRequest
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
            'lineId' => 'required|numeric', 
            'devId' => 'required|numeric', 
            'appId' => 'required|string', 
            'status' =>  'required|in:0,1',
            'created_by'  =>'required|numeric', 
        ];
    }

    public function messages()
    {
        return [
            // 'cenId.required' => 'The CenterId is required.',
            'lineId.required' => 'The lineId field must be a number.',
             'devId.required' => 'The devId field must be a number.',
              'appId.required' => 'The appId field must be a string.',
            'status.required' => 'The status field is required.',
            
        ];
    }


}
