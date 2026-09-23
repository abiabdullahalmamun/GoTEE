<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualReceiveRequest  extends FormRequest
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
      
        $this->merge([
            'created_by' => auth()->user()->id,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }

  
    public function rules()
    {
        return [
            'Webfile' => [
                    'required',
                    'string',
                    'regex:/^BGD.{9}$/'
                ],
        
            'name' => 'required|string',
            'passport' => 'required|string',
            'contact' => 'required|string',
            'stickertype' => 'required|numeric',
            'stickerNo' => 'required|string',
            'visatype' => 'required|numeric',
            'created_by' => 'required|numeric',
            'rec_date' => 'required|date|before_or_equal:today',

         ];
    }

  
    public function messages()
    {
        return [
            'Webfile.regex' => 'The WebFile_no must start with BGD and be exactly 12 characters.',
            'contact.required' => 'Contact Number must be number starting with 0',
            'name.required' => 'The name is required.',
            'passport.required' => 'The passport is required.',
            'stickertype.required' => 'The stickertype is required.',
            'stickerNo.required' => 'The stickerNo is required.',
            'visatype.required' => 'The visatype type is required.',
           
        ];
    }


}
