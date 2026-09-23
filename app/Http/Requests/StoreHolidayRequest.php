<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHolidayRequest  extends FormRequest
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
            'from_date' => 'required|date', 
            'to_date' => 'required|date', 
            'weekday' => 'nullable|string', 
            'description' => 'required|string', 
            'created_by'  =>'required|numeric', 
        ];
    }

    public function messages()
    {
        return [
            'from_date.required' => 'The from_date is required.',
            'to_date.required' => 'The to_date required.',
            'description.required' => 'The description field is required.',
            
        ];
    }


}
