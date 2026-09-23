<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCenterRequest extends FormRequest
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
            'regionId' => 'required|numeric', 
            'centerName' => 'required|string', 
            'startHr' => 'required|string', 
            'endHr' => 'required|string', 
            'apt_tol' => 'required|numeric', 
            'end_tol' => 'required|numeric', 
            'del_time' => 'required|string', 
            'gtw_name' => 'required|string', 
            'hotline' => 'nullable|string', 
            'info' => 'nullable|string', 
            'created_by' => 'nullable|numeric',
            'created_at' => 'nullable|date',
            'cenId' => 'required|integer|min:1',
             'status' =>  'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'regionId.required' => 'The Region Name is required.',
            'centerName.required' => 'The centerName field must be a string.',
            'startHr.required' => 'The Start Hr field is required.',
            'endHr.required' => 'The End Hr field is required.',
            'apt_tol.required' => 'The Tolerance must be in minutes.',
             'end_tol.required' => 'The after Tolerance must be in minutes.',
            'del_time.required' => 'The Delivery time field is required.',
            'gtw_name.required' => 'The fateway name field is required.',
        ];
    }
}
