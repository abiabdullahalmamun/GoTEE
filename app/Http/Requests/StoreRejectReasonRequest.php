<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRejectReasonRequest  extends FormRequest
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
            'reasonName' => 'required|string', 
            'status' =>  'required|in:0,1',
            'created_by'  =>'required|numeric', 
          
        ];
    }

    public function messages()
    {
        return [
            'reasonName.required' => 'The reasonName is required.',
            'status.required' => 'The status field is required.',
            
        ];
    }


}
