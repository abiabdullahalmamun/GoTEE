<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOperationListRequest  extends FormRequest
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
            // Order information
            'IsLastOperation' => 'nullable|numeric',
            'OrderCode' => 'required|numeric|exists:tbl_order_no,OrderCode',
            // Operation details (arrays)
            'OperationSeqNo'   => 'required|array|min:1',
            'OperationSeqNo.*' => 'required|numeric|min:1|distinct',

            'OperationCode'   => 'required|array|min:1',
            'OperationCode.*' => 'required|numeric|max:100000|distinct:strict|exists:tbl_operationCode,OprNo',

            'OperationName'   => 'required|array|min:1',
            'OperationName.*' => 'required|string|max:255',

            'MC_Type'   => 'required|array|min:1',
            'MC_Type.*' => 'nullable|numeric|exists:tbl_machine_type,id',

            'SMV'   => 'required|array|min:1',
            'SMV.*' => 'required|numeric|min:0',

            'Target'   => 'required|array|min:1',
            'Target.*' => 'required|numeric|min:0',

            'ManPower'   => 'required|array|min:1',
            'ManPower.*' => 'required|numeric|min:0',

            'grpCode'   => 'required|array|min:1',
            'grpCode.*' => 'nullable|numeric|exists:tbl_process_grp,id',

             'created_by'  =>'required|numeric', 
        ];
    }

    public function messages()
    {
        return [
            'OrderCode.required' => 'Order Code is required.',

            'OperationSeqNo.*.required' => 'Operation sequence is required.',
            'OperationCode.*.required' => 'Operation code is required.',
            'OperationName.*.required' => 'Operation name is required.',
            'MC_Type.*.required' => 'Machine type is required.',
            'SMV.*.required' => 'SMV is required.',
            'Target.*.required' => 'Target is required.',
            'ManPower.*.required' => 'Manpower is required.',
            'grpCode.*.required' => 'Process group is required.',

            'SMV.*.numeric' => 'SMV must be a valid number.',
            'Target.*.numeric' => 'Target must be a valid number.',
            'ManPower.*.numeric' => 'Manpower must be a valid number.',
        ];
    }


}
