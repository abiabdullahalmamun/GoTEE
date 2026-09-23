<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppReceiveRequest  extends FormRequest
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
            'wf_no' => [
                    'required',
                    'string',
                    'regex:/^BGD.{9}$/'
                ],
        
            'name' => 'required|string',
            'passport1' => 'required|string',
            'passport2' => 'nullable|string',
             'passport3' => 'nullable|string',
            'op_type' => 'nullable|string',
            // 'contact' => 'required|string',
                'contact' => [
                    'required',
                    'string',
                    'regex:/^0.{10}$/'
                ],             
            'svctypeId' => 'required|numeric',
            'svcType' => 'required|numeric',
            'comment' => 'nullable|string',
            // 'otp' => 'required|numeric',
            'fmember' => 'nullable|string',
            'visaType' => 'required|numeric',
            'st_color' => 'required|numeric',
            'OldPass' => 'required|numeric',
            'sticker_no' => 'required|string',
            'bio_st' => 'required|numeric',
            'remarks' => 'nullable|string',
            'TokenNo' => 'required|numeric',
            'counterNo' => 'required|numeric',
            'corfee' => 'nullable|numeric',
            'profee' => 'nullable|numeric',
             'spfee' => 'nullable|numeric',
            'payment_method' => 'required|in:1,2,3',
            'code' => 'required|numeric',
            'created_by' => 'required|numeric',
           
         ];
    }

  
    public function messages()
    {
        return [
            'wf_no.regex' => 'The WebFile_no must start with BGD and be exactly 12 characters.',
            'contact.required' => 'Contact Number must be number starting with 0',
            'name.required' => 'The name is required.',
            'TokenNo.required' => 'The TokenNo is required.',
            'counterNo.required' => 'The counterNo is required.',
            'passport1.required' => 'The passport is required.',
            // 'otp.required' => 'The otp is required.',
            'visaType.required' => 'The visaType is required.',
            'st_color.required' => 'The sticker type is required.',
            'sticker_no.required' => 'The visaType is required.',
            'bio_st.required' => 'The bio_st is required.',
            'payment_method.required' => 'The payment_method is required.',
            'datremarkse.required' => 'The remarks is required.',
            'svcType.required' => 'The svcType is required.',
              'svctypeId.required' => 'The svctypeId is required.',
        ];
    }


}
