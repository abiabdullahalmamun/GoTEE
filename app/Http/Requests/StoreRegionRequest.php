<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest  extends FormRequest
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

    // public function rules(): array
    // {
    //     return [
    //         'PrintedSerial' => 'required|string|max:255',
    //         'tag' => 'required|string|max:255',
    //     ];
    // }

    public function rules()
    {
        return [
            'region_name' => 'required|string', 
            'region_text' => 'required|string', 
            'created_by' => 'nullable|numeric',
            'created_at' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'region_name.required' => 'The Tag is required.',
            'region_name.string' => 'The Tag field must be a string.',
            'region_text.required' => 'The PrintedSerial field is required.',
            'region_text.string' => 'The PrintedSerial field should be string.',
        ];
    }


}
