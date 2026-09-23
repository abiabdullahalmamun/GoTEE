<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
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
            'cenId' => 'required|numeric', 
            'devType' => 'required|numeric', 
            'mac' => 'required|string', 
            'ip' => 'nullable|string', 
            'location' => 'nullable|nullable', 
            'status' =>  'required|in:0,1',
             'led' =>  'required|in:1,2,3,4,5,6,7,8',
              'message' =>  'nullable|string',
            // 'created_by'  =>'required|numeric', 

        ];
    }

    public function messages()
    {
        return [
            'recId.required' => 'The recId is required.',
            'cenId.required' => 'The CenterId is required.',
            'devType.required' => 'The devType field must be a number.',
            'mac.required' => 'The mac field is required.',
            'status.required' => 'The status field is required.',
             'led.required' => 'The led field is required.',
            // 'created_by.required' => 'The status field is required.',
   
        ];
    }
}
