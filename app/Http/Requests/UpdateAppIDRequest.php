<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppIDRequest extends FormRequest
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
                'lineId' => 'required|numeric', 
            'devId' => 'required|numeric', 
            'appId' => 'required|string', 
            'status' =>  'required|in:0,1',
            // 'created_by'  =>'required|numeric', 
          'recId' => 'required|numeric', 
      
        ];
    }

    public function messages()
    {
        return [
            'recId.required' => 'The recId is required.',
            'lineId.required' => 'The lineId is required.',
            'devId.required' => 'The devId field must be a number.',
            'appId.required' => 'The appId field is required.',
            'status.required' => 'The status field is required.',
        
        ];
    }
}
