<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest  extends FormRequest
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
            'devID' => 'required|numeric', 
            'devType' => 'required|numeric', 
            'mac' => 'required|string', 
            'ip' => 'nullable|string', 
            'location' => 'nullable|string', 
            'status' =>  'required|in:0,1',
            'created_by'  =>'required|numeric', 
            // 'serviceId'   => 'nullable|array',
            // 'serviceId'   => 'nullable|array|max:2',     
            // 'serviceId.*' => 'exists:tbl_service,id',
        ];
    }

    public function messages()
    {
        return [
            // 'cenId.required' => 'The CenterId is required.',
            'devType.required' => 'The devType field must be a number.',
            'mac.required' => 'The mac field is required.',
            'status.required' => 'The status field is required.',
            
        ];
    }


}
