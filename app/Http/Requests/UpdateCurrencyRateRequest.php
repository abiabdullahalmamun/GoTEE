<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRateRequest extends FormRequest
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
            'nameD' => 'required|string', 
            'status' =>  'required|in:0,1',
            // 'created_by'  =>'required|numeric', 

        ];
    }

    public function messages()
    {
        return [
            'recId.required' => 'The recId is required.',
            'nameD.required' => 'The CenterId is required.',
              'status.required' => 'The status field is required.',
       
        ];
    }
}
