<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForeignReceiveRequest  extends FormRequest
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
            'op_type' => 'nullable|string',
            // 'contact' => 'required|string',
                'contact' => [
                    'required',
                    'string',
                    'regex:/^0.{10}$/'
                ],             


            // 'otp' => 'required|numeric',
            'TokenNo' => 'nullable|numeric',
            'visaType' => 'required|numeric',
            'st_color' => 'required|numeric',
            'OldPass' => 'required|numeric',
            'sticker_no' => 'required|string',
            'bio_st' => 'required|numeric',
            'remarks' => 'nullable|string',
              'nationality' => 'nullable|string',
            // 'TokenNo' => 'required|numeric',
            'counterNo' => 'required|numeric',
            'corfee' => 'nullable|numeric',
             'gratis' =>  'required|in:0,1',
             'BookNo' =>  'required|numeric',
             'RecptNo' => 'nullable|numeric',
             'visafee' =>  'required|numeric',
             'icwf' =>  'required|numeric',
             'faxcharge' =>  'required|numeric',
             'visaApp' =>  'required|numeric',
              'txnId' =>  'nullable|string',
             'totalfee' =>  'required|numeric',
            'payment_method' => 'required|in:1,2,3',
            'code' => 'required|numeric',
             'rupee_rate' => 'required|numeric',
            'created_by' => 'required|numeric',    
            'duration' => 'required|numeric',    
            'entryType' => 'required|numeric',           
         ];
    }
  
    public function messages()
    {
        return [
            'wf_no.regex' => 'The WebFile_no must start with BGD and be exactly 12 characters.',
            'contact.required' => 'Contact Number must be number starting with 0',
            'name.required' => 'The name is required.',
           'nationality.required' => 'The nationality is required.',
            'counterNo.required' => 'The counterNo is required.',
            'passport1.required' => 'The passport is required.',
           
            'visaType.required' => 'The visaType is required.',
            'st_color.required' => 'The sticker type is required.',
            'sticker_no.required' => 'The sticker_no is required.',
            'gratis.required' => 'The gratis is required.',
            'BookNo.required' => 'The BookNo is required.',
            'RecptNo.required' => 'The RecptNo is required.',
            'visafee.required' => 'The visafee is required.',
            'icwf.required' => 'The icwf is required.',
            'faxcharge.required' => 'The faxcharge is required.',
            'visaApp.required' => 'The visaApp is required.',
            'totalfee.required' => 'The totalfee is required.',
  'duration.required' => 'The totalfee is required.',
  'entryType.required' => 'The totalfee is required.',
            'bio_st.required' => 'The bio_st is required.',
            'payment_method.required' => 'The payment_method is required.',
            'datremarkse.required' => 'The remarks is required.',
           
        ];
    }


}
