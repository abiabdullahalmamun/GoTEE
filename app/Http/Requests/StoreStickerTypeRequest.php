<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStickerTypeRequest  extends FormRequest
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
            'StickerInfo' => 'nullable|string', 
            'sticker' => 'required|string', 
            'center_id' => 'required|numeric', 
             'remarks' => 'nullable|string', 
            'created_by'  =>'required|numeric', 
        ];
    }

    public function messages()
    {
        return [
            'sticker.required' => 'The sticker is required.',
            'center_id.required' => 'The center_id required.',
            'created_by.required' => 'The created_by field is required.',
            
        ];
    }


}
