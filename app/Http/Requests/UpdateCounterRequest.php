<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCounterRequest extends FormRequest
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
            'cenId' => 'required|numeric', 
            'counterId' => 'required|numeric', 
            'counterNo' => 'required|numeric', 
            'counterName' => 'required|string', 
            'mac' => 'required|string', 
            'ip' => 'nullable|string', 
            'hostname' => 'nullable|string', 
            'counterId' => 'required|integer|min:1',
            'created_by' => 'nullable|numeric',
            'created_at' => 'nullable|date',
             'status' =>  'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
           'cenId.required' => 'The CenterId is required.',
            'counterNo.required' => 'The counterNo field must be a number.',
            'counterName.required' => 'The counterName field is required.',
            'mac.required' => 'The mac field is required.',
        ];
    }
}
