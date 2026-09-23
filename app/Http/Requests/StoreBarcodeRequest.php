<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarcodeRequest  extends FormRequest
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
            'centerId' => 'required|numeric', 
            'sticker' => 'required|numeric', 
             'start_number' => 'required|numeric', 
              'end_number' => 'required|numeric', 
            'created_by' => 'nullable|numeric',
            'date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'centerId.required' => 'The centerId is required.',
             'sticker.required' => 'The sticker is required.',
              'start_number.required' => 'The start_number is required.',
               'end_number.required' => 'The end_number is required.',
                'date.required' => 'The date is required.',
        ];
    }


}
