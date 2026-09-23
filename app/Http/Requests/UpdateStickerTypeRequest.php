<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStickerTypeRequest extends FormRequest
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
 
    }

    public function rules()
    {
        return [
            
            'StickerInfo' => 'nullable|string', 
            'sticker' => 'required|string', 
            'center_id' => 'required|numeric', 
            'stcId' => 'required|numeric', 
            'remarks' => 'nullable|string', 
         
        ];
    }

    public function messages()
    {
        return [
           // 'StickerInfo.required' => 'The StickerInfo is required.',
            'sticker.required' => 'The sticker field must be a number.',
            'center_id.required' => 'The center_id field is required.',
             'stcId.required' => 'The stcId field is required.',
            // 'remarks.required' => 'The remarks field is required.',
        ];
    }
}
